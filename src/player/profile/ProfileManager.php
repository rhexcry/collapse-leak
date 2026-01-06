<?php

declare(strict_types=1);

namespace collapse\player\profile;

use collapse\i18n\CollapseTranslationFactory;
use collapse\mongo\MongoUtils;
use collapse\mongo\MongoWrapper;
use collapse\mongo\operation\FindOneOperation;
use collapse\mongo\operation\FindOperation;
use collapse\mongo\operation\InsertOneOperation;
use collapse\mongo\operation\ReplaceOneOperation;
use collapse\mongo\promise\MongoPromise;
use collapse\player\CollapsePlayer;
use collapse\player\profile\command\AltsCommand;
use collapse\player\profile\command\ProfileCommand;
use collapse\player\profile\event\ProfileLoadedEvent;
use collapse\player\rank\Rank;
use collapse\Practice;
use collapse\punishments\Punishment;
use collapse\punishments\rule\PunishmentRules;
use MongoDB\BSON\ObjectId;
use MongoDB\Collection;
use MongoDB\Model\BSONArray;
use MongoDB\Model\BSONDocument;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\network\mcpe\handler\PreSpawnPacketHandler;
use pocketmine\network\mcpe\protocol\RequestChunkRadiusPacket;
use pocketmine\player\XboxLivePlayerInfo;
use function count;
use function microtime;
use function round;
use function strtolower;
use function time;

final readonly class ProfileManager{

	public const string COLLECTION = 'profiles';

	private Collection $collection;

	private \PrefixedLogger $logger;

	public function __construct(private Practice $plugin){
		$this->collection = MongoWrapper::getClient()->selectCollection(Practice::getDatabaseName(), self::COLLECTION);
		$this->collection->createIndex(['xuid' => -1]);
		$this->logger = new \PrefixedLogger($this->plugin->getLogger(), 'Profiles');
		$this->plugin->getServer()->getPluginManager()->registerEvents(new ProfileListener($this), $this->plugin);
		$this->plugin->getServer()->getCommandMap()->registerAll('collapse', [
			new AltsCommand($this),
			new ProfileCommand($this)
		]);
	}

	public function getPlugin() : Practice{
		return $this->plugin;
	}

	public function getProfileByName(string $playerName) : ?Profile{
		$result = $this->collection->findOne(['lowerCasePlayerName' => strtolower($playerName)]);
		if($result instanceof BSONDocument){
			return Profile::fromBsonDocument($result);
		}
		return null;
	}

	public function getProfileByXuid(string $xuid) : ?Profile{
		$result = $this->collection->findOne(['xuid' => $xuid]);
		if($result instanceof BSONDocument){
			return Profile::fromBsonDocument($result);
		}
		return null;
	}

	public function getAllStaffProfiles() : MongoPromise{
		$staffRankValues = array_map(fn(Rank $rank) => $rank->value, array_filter(Rank::cases(),
			fn(Rank $rank) => $rank->isStaffRank()
		));
		$staffRankValues = array_values($staffRankValues);
		return MongoWrapper::push(new FindOperation(
			Practice::getDatabaseName(),
			self::COLLECTION,
			['rank' => ['$in' => $staffRankValues]]
		));
	}

	public function onPlayerConnect(CollapsePlayer $player, RequestChunkRadiusPacket $packet) : void{
		$player->setWaitingProfileLoad();
		$playerInfo = $player->getPlayerInfo();
		if(!$playerInfo instanceof XboxLivePlayerInfo){
			throw new \InvalidArgumentException('Player must be authorized in Xbox');
		}

		$start = microtime(true);
		MongoWrapper::push(new FindOneOperation($this->collection->getDatabaseName(), $this->collection->getCollectionName(), ['xuid' => $playerInfo->getXuid()]))->onResolve(
			function(array|null|object $result) use ($player, $packet, $start, $playerInfo) : void{
				if(!($player instanceof CollapsePlayer && $player->isConnected())){
					return;
				}
				$this->onProfileProcess($result, $player, $packet, $start);
			}
		);
	}

	private function onProfileLoaded(CollapsePlayer $player, Profile $profile, RequestChunkRadiusPacket $packet) : void{
		$player->setWaitingProfileLoad(false);
		$player->setProfile($profile);
		(new ProfileLoadedEvent($profile))->call();
		$handler = $player->getNetworkSession()->getHandler();
		if($handler instanceof PreSpawnPacketHandler){
			$handler->handleRequestChunkRadius($packet);
		}
		$player->getNetworkSession()->syncAvailableCommands();
		$this->plugin->getSkinManager()->save($player);
	}

	private function onProfileProcess(?BSONDocument $result, CollapsePlayer $player, RequestChunkRadiusPacket $packet, float $start) : void{
		if($result instanceof BSONDocument){
			$profile = Profile::fromBsonDocument($result);
			$this->logger->notice('Profile ' . $player->getName() . ' - ' . $profile->getId() . ' fetched in ' . round(microtime(true) - $start, 5) . 'ms');
			//Update section
			$profile->set('playerName', $player->getName());
			$profile->set('lowerCasePlayerName', strtolower($player->getName()));
			$profile->set('ip', $player->getNetworkSession()->getIp());
			$profile->set('device_id', $player->getPlayerInfo()->getExtraData()['DeviceId']);
			$profile->set('device_model', $player->getPlayerInfo()->getExtraData()['DeviceModel']);
			$profile->set('device_os', $player->getPlayerInfo()->getExtraData()['DeviceOS']);
			$profile->set('input_mode', $player->getPlayerInfo()->getExtraData()['CurrentInputMode']);
			if($profile->get('first_join_time') === null){
				$profile->set('first_join_time', time());
			}
			$profile->set('game_version', $player->getPlayerInfo()->getExtraData()['GameVersion']);
			$profile->setTotalMp($result['total_mp'] ?? 0);
			$profile->setIssuedPunishments(MongoUtils::bsonArrayToArray($result['issued_punishments'] ?? new BSONArray([])) ?? []);
			$profile->setOnlineMinutes(MongoUtils::bsonArrayToArray($result['online_minutes'] ?? new BSONArray([])) ?? []);
			//End of update section
			$this->onProfileLoaded($player, $profile, $packet);
		}else{
			$profile = Profile::create($player);
			MongoWrapper::push(new InsertOneOperation($this->collection->getDatabaseName(), $this->collection->getCollectionName(), $profile->export()))->onResolve(
				function(?ObjectId $insertedId) use ($player, $packet, $start, $profile) : void{
					$this->logger->notice('Profile ' . $player->getName() . ' - ' . ($insertedId ?? 'NULL') . ' created in ' . round(microtime(true) - $start, 5) . 'ms');
					$profile->onInsert($insertedId);
					$this->onProfileLoaded($player, $profile, $packet);
				}
			);
		}
	}

	public function findAltsAccounts(Profile $profile) : MongoPromise{
		$promise = new MongoPromise();
		$results = [];
		$onResolve = function() use ($promise, &$results) : void{
			if(count($results) === 2){
				$promise->resolve($results);
			}
		};
		MongoWrapper::push(new FindOperation(
			$this->collection->getDatabaseName(),
			$this->collection->getCollectionName(),
			['ip' => $profile->getIp()]
		))->onResolve(function(array $result) use (&$results, $onResolve) : void{
			$results['ip'] = $result;
			$onResolve();
		});
		MongoWrapper::push(new FindOperation(
			$this->collection->getDatabaseName(),
			$this->collection->getCollectionName(),
			['device_id' => $profile->getDeviceId()]
		))->onResolve(function(array $result) use (&$results, $onResolve) : void{
			$results['device_id'] = $result;
			$onResolve();
		});
		return $promise;
	}

	public function onPlayerDisconnect(CollapsePlayer $player) : void{
		$profile = $player->getProfile();
		if($profile !== null){
			$this->saveProfile($profile);
		}
	}

	public function close() : void{
		foreach(Practice::onlinePlayers() as $player){
			$profile = $player->getProfile();
			if($profile === null){
				continue;
			}
			$this->collection->replaceOne(['xuid' => $profile->getXuid()], $profile->export());
		}
	}

	public function saveProfile(Profile $profile) : void{
		$start = microtime(true);
		MongoWrapper::push(
			new ReplaceOneOperation(
				$this->collection->getDatabaseName(),
				$this->collection->getCollectionName(),
				['xuid' => $profile->getXuid()],
				$profile->export()
			)
		)->onResolve(
			function() use ($profile, $start) : void{
				$this->logger->debug('Profile ' . $profile->getPlayerName() . ' saved in ' . round(microtime(true) - $start, 5) . 'ms');
			}
		);
	}

	public function onPlayerPreLogin(PlayerPreLoginEvent $event) : void{
		$playerInfo = $event->getPlayerInfo();
		if(!$playerInfo instanceof XboxLivePlayerInfo){
			$event->setKickFlag(PlayerPreLoginEvent::KICK_FLAG_PLUGIN, 'Player must be authorized in Xbox');
			return;
		}

		$punishment = $this->plugin->getPunishmentManager()->getBanPunishmentByXuid($playerInfo->getXuid());
		if($punishment === null){
			return;
		}

		$translator = $this->plugin->getTranslatorManager()->fromLocale($event->getPlayerInfo()->getLocale());
		$rule = PunishmentRules::getRule($punishment->getReason());
		$kickMessage = $translator->translate(CollapseTranslationFactory::ban_disconnect_screen_message(
			$punishment->getPlayerName(),
			$rule?->getTranslation(true) ?? $punishment->getReason(),
			(new \DateTime())->setTimestamp($punishment->getCreation())->format(Punishment::DATE_TIME_FORMAT),
			$this->plugin->getPunishmentManager()->convertExpires($punishment)
		));

		$event->setKickFlag(PlayerPreLoginEvent::KICK_FLAG_PLUGIN, $kickMessage);
	}
}

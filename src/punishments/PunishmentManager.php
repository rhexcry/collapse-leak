<?php

declare(strict_types=1);

namespace collapse\punishments;

use collapse\i18n\CollapseTranslationFactory;
use collapse\mongo\MongoWrapper;
use collapse\mongo\operation\FindOneOperation;
use collapse\mongo\operation\InsertOneOperation;
use collapse\mongo\promise\MongoPromise;
use collapse\player\CollapsePlayer;
use collapse\player\profile\Profile;
use collapse\Practice;
use collapse\punishments\command\BanCommand;
use collapse\punishments\command\KickCommand;
use collapse\punishments\command\MuteCommand;
use collapse\punishments\command\UnbanCommand;
use collapse\punishments\command\UnmuteCommand;
use collapse\punishments\event\ProfilePreKickEvent;
use collapse\punishments\event\ProfilePrePunishEvent;
use collapse\punishments\rule\PunishmentRules;
use collapse\utils\TimeUtils;
use MongoDB\Collection;
use MongoDB\Model\BSONDocument;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\XboxLivePlayerInfo;
use Symfony\Component\Filesystem\Path;
use function strtolower;
use function time;

final class PunishmentManager{

	private const string COLLECTION = 'punishments';
	private const string RULES_CONFIG_PATH = 'punishments/rules.json';

	private Collection $collection;

	private array $banQueue = [];
	private array $muteQueue = [];

	public function __construct(private readonly Practice $plugin){
		$this->collection = MongoWrapper::getClient()->selectCollection(Practice::getDatabaseName(), self::COLLECTION);
		$this->plugin->saveResource(self::RULES_CONFIG_PATH);
		PunishmentRules::init(Path::join($this->plugin->getDataFolder(), self::RULES_CONFIG_PATH));
		$this->plugin->getServer()->getPluginManager()->registerEvents(new PunishmentListener($this), Practice::getInstance());
		$this->plugin->getServer()->getCommandMap()->registerAll('collapse', [
			new BanCommand($this),
			new KickCommand($this),
			new MuteCommand($this),
			new UnbanCommand($this),
			new UnmuteCommand($this)
		]);
	}

	private function isPunishedByXuid(string $xuid, PunishmentType $type) : bool{
		$result = $this->collection->findOne(['type' => $type->value, 'xuid' => $xuid]);
		if($result instanceof BSONDocument){
			return (Punishment::fromBsonDocument($result))->isActive();
		}
		return false;
	}

	private function isPunishedByName(string $playerName, PunishmentType $type) : bool{
		$result = $this->collection->findOne(['type' => $type->value, 'lowerCasePlayerName' => strtolower($playerName)]);
		if($result instanceof BSONDocument){
			return (Punishment::fromBsonDocument($result))->isActive();
		}
		return false;
	}

	private function getPunishmentByXuid(string $xuid, PunishmentType $type) : ?Punishment{
		$result = $this->collection->findOne(['type' => $type->value, 'xuid' => $xuid]);
		if($result instanceof BSONDocument){
			$punishment = (Punishment::fromBsonDocument($result));
			if(!$punishment->isActive()){
				$this->removePunishment($punishment, $punishment->getType());
				return null;
			}
			return $punishment;
		}
		return null;
	}

	private function getPunishmentByName(string $playerName, PunishmentType $type) : ?Punishment{
		$result = $this->collection->findOne(['type' => $type->value, 'lowerCasePlayerName' => strtolower($playerName)]);
		if($result instanceof BSONDocument){
			$punishment = (Punishment::fromBsonDocument($result));
			if(!$punishment->isActive()){
				$this->removePunishment($punishment, $punishment->getType());
				return null;
			}
			return $punishment;
		}
		return null;
	}

	public function isBannedByXuid(string $xuid) : bool{
		return $this->isPunishedByXuid($xuid, PunishmentType::Ban);
	}

	public function isBannedByName(string $playerName) : bool{
		return $this->isPunishedByName($playerName, PunishmentType::Ban);
	}

	public function getBanPunishmentByXuid(string $xuid) : ?Punishment{
		return $this->getPunishmentByXuid($xuid, PunishmentType::Ban);
	}

	public function getBanPunishmentByName(string $playerName) : ?Punishment{
		return $this->getPunishmentByName($playerName, PunishmentType::Ban);
	}

	public function isMutedByXuid(string $xuid) : bool{
		return $this->isPunishedByName($xuid, PunishmentType::Mute);
	}

	public function isMutedByName(string $playerName) : bool{
		return $this->isPunishedByName($playerName, PunishmentType::Mute);
	}

	public function getMutePunishmentByXuid(string $xuid) : ?Punishment{
		return $this->getPunishmentByXuid($xuid, PunishmentType::Mute);
	}

	public function getMutePunishmentByName(string $playerName) : ?Punishment{
		return $this->getPunishmentByName($playerName, PunishmentType::Mute);
	}

	public function convertExpires(Punishment $punishment) : Translatable{
		if($punishment->getExpiration() === null){
			return CollapseTranslationFactory::punishment_expires_never();
		}
		return TimeUtils::convert($punishment->getExpiration() - time(), true);
	}

	public function onPlayerConnect(CollapsePlayer $player) : MongoPromise{
		$playerInfo = $player->getPlayerInfo();
		if(!$playerInfo instanceof XboxLivePlayerInfo){
			throw new \InvalidArgumentException('Player must be authorized in Xbox');
		}
		$promise = new MongoPromise();
		MongoWrapper::push(new FindOneOperation(
			$this->collection->getDatabaseName(),
			$this->collection->getCollectionName(),
			[
				'$or' => [
					['xuid' => $playerInfo->getXuid()],
					['device_id' => $playerInfo->getExtraData()['DeviceId']]
				],
				'type' => PunishmentType::Ban->value
			]
		))->onResolve(
			function(array|object|null $result) use ($promise) : void{
				if($result instanceof BSONDocument){
					$promise->resolve(Punishment::fromBsonDocument($result));
					return;
				}
				$promise->resolve(null);
			}
		);
		return $promise;
	}

	/**
	 * @param \Closure(Punishment $punishment) : void $onCompletion
	 */
	private function onPunishment(PunishmentType $type, Profile $profile, string $reason, ?CommandSender $sender, ?int $expires, array &$queue, \Closure $onCompletion) : void{
		$punishment = Punishment::createFromProfile($type, $profile, $reason, $sender, $expires);
		if($sender !== null){
			$ev = new ProfilePrePunishEvent($profile, $punishment, $sender);
			$ev->call();
			if($ev->isCancelled()){
				return;
			}

			if(isset($queue[$profile->getLowerCasePlayerName()])){
				return;
			}

			$queue[$profile->getLowerCasePlayerName()] = true;
			MongoWrapper::push(new InsertOneOperation($this->collection->getDatabaseName(), $this->collection->getCollectionName(), $punishment->export()))->onResolve(
				function() use ($profile, &$queue, $onCompletion, $punishment) : void{
					unset($queue[$profile->getLowerCasePlayerName()]);
					$onCompletion($punishment);
					$this->plugin->getSocialManager()->getStaffLogger()->onPunishment(
						$punishment,
						$this->convertExpires($punishment)
					);
				}
			);
		}
	}

	private function broadcastSeparatedMessage(Translatable $forAdmins, Translatable $forPlayers) : void{
		$admins = [];
		$players = [];
		foreach(Practice::onlinePlayers() as $target){
			if($target?->getProfile()?->getRank()->isStaffRank()){
				$admins[] = $target;
			}else{
				$players[] = $target;
			}
		}
		$this->plugin->getTranslatorManager()->broadcastTranslatedMessage($forAdmins, $admins, false);
		foreach($players as $target){
			if($target->isConnected()){
				$target->sendTranslatedMessage($forPlayers, false);
			}
		}
	}

	public function ban(Profile $profile, string $reason, ?CommandSender $sender, ?int $expires, \Closure $onCompletion) : void{
		$this->onPunishment(
			PunishmentType::Ban,
			$profile,
			$reason,
			$sender,
			$expires,
			$this->banQueue,
			function(Punishment $punishment) use ($profile, $sender, $expires, $onCompletion) : void{
				$player = $profile->getPlayer();
				$player?->kick($profile->getTranslator()->translate(CollapseTranslationFactory::ban_disconnect_screen_message_now(
					$punishment->getPlayerName(),
					PunishmentRules::getRule($punishment->getReason())?->getTranslation(true) ?? $punishment->getReason(),
					(new \DateTime())->setTimestamp($punishment->getCreation())->format(Punishment::DATE_TIME_FORMAT),
					$this->convertExpires($punishment)
				)));
				if($sender !== null){
					$this->broadcastSeparatedMessage(
						CollapseTranslationFactory::ban_broadcast_admins(
							$punishment->getPlayerName(),
							$punishment->getSender(),
							$punishment->getReason(),
							$this->convertExpires($punishment)
						),
						CollapseTranslationFactory::ban_broadcast_players(
							$punishment->getPlayerName(),
							$punishment->getReason()
						)
					);
					$onCompletion();
				}
			});
	}

	public function kick(CollapsePlayer $player, string $reason, ?CommandSender $sender) : void{
		$ev = new ProfilePreKickEvent($player->getProfile(), $reason, $sender);
		$ev->call();
		if($ev->isCancelled()){
			return;
		}
		$rule = PunishmentRules::getRule($reason);
		$translatedReason = $rule?->getTranslation(true) ?? $reason;

		$player->kick($player->getProfile()->getTranslator()->translate(CollapseTranslationFactory::kick_disconnect_screen_message(
			$translatedReason
		)));
		if($sender !== null){
			$this->broadcastSeparatedMessage(
				CollapseTranslationFactory::kick_broadcast_admins(
					$player->getName(),
					$sender->getName(),
					$translatedReason
				),
				CollapseTranslationFactory::kick_broadcast_players($player->getName())
			);
			$this->plugin->getSocialManager()->getStaffLogger()->onKick($player, $reason, $sender);
		}
	}

	public function mute(Profile $profile, string $reason, ?CommandSender $sender, ?int $expires, \Closure $onCompletion) : void{
		$this->onPunishment(
			PunishmentType::Mute,
			$profile,
			$reason,
			$sender,
			$expires,
			$this->muteQueue,
			function(Punishment $punishment) use ($profile, $reason, $sender, $expires, $onCompletion) : void{
				$player = $profile->getPlayer();
				$player?->sendTranslatedMessage(CollapseTranslationFactory::chat_muted(
					$punishment->getReason(),
					$this->convertExpires($punishment)
				));
				$player?->setMutePunishment($punishment);
				if($sender !== null){
					$this->broadcastSeparatedMessage(
						CollapseTranslationFactory::mute_broadcast_admins(
							$punishment->getPlayerName(),
							$punishment->getSender(),
							PunishmentRules::getRule($punishment->getReason())?->getTranslation(true) ?? $punishment->getReason(),
							$this->convertExpires($punishment)
						),
						CollapseTranslationFactory::mute_broadcast_players(
							$punishment->getPlayerName(),
							$punishment->getReason()
						)
					);
					$onCompletion();
				}
			});
	}

	private function removePunishment(Punishment $punishment, PunishmentType $type) : void{
		$this->collection->deleteMany(['type' => $type->value, 'xuid' => $punishment->getXuid()]);
	}

	public function unban(Punishment $punishment, ?CommandSender $sender) : void{
		$this->removePunishment($punishment, PunishmentType::Ban);
		if($sender !== null){
			$this->broadcastSeparatedMessage(
				CollapseTranslationFactory::unban_broadcast_admins(
					$punishment->getPlayerName(),
					$sender->getName()
				),
				CollapseTranslationFactory::unban_broadcast_players($punishment->getPlayerName())
			);
			$this->plugin->getSocialManager()->getStaffLogger()->onUnban($punishment, $sender);
		}
	}

	public function unmute(Punishment $punishment, ?CommandSender $sender) : void{
		$this->removePunishment($punishment, PunishmentType::Mute);
		if($sender !== null){
			$this->broadcastSeparatedMessage(
				CollapseTranslationFactory::unmute_broadcast_admins(
					$punishment->getPlayerName(),
					$sender->getName()
				),
				CollapseTranslationFactory::unmute_broadcast_players($punishment->getPlayerName())
			);
			$this->plugin->getSocialManager()->getStaffLogger()->onUnmute($punishment);
		}
		$player = Practice::getPlayerExact($punishment->getLowerCasePlayerName());
		$player?->setMutePunishment(null);
	}
}

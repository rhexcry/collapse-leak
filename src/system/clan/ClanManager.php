<?php

declare(strict_types=1);

namespace collapse\system\clan;

use collapse\mongo\MongoWrapper;
use collapse\mongo\operation\InsertOneOperation;
use collapse\mongo\operation\ReplaceOneOperation;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\system\clan\concrete\Clan;
use collapse\system\clan\concrete\ClanRole;
use collapse\system\clan\event\ClanCreatedEvent;
use collapse\system\clan\event\ClanDisbandedEvent;
use collapse\system\clan\listener\ClanListener;
use collapse\system\clan\types\ClanError;
use MongoDB\BSON\ObjectId;
use MongoDB\Collection;
use MongoDB\Model\BSONDocument;
use function microtime;
use function round;
use function strtolower;

final class ClanManager{

	private const string COLLECTION = 'clans';

	private Collection $collection;
	private \PrefixedLogger $logger;

	public function __construct(private readonly Practice $plugin){
		$this->collection = MongoWrapper::getClient()->selectCollection(Practice::getDatabaseName(), self::COLLECTION);
		$this->collection->createIndex(['name' => -1]);
		$this->collection->createIndex(['tag' => -1]);
		$this->logger = new \PrefixedLogger($this->plugin->getLogger(), 'Clans');
		/*$this->plugin->getServer()->getCommandMap()->registerAll('collapse', [
			new ClanCommand($this)
		]);*/
		$this->plugin->getServer()->getPluginManager()->registerEvents(new ClanListener($this), $this->plugin);
	}

	public function getClanByName(string $name) : ?Clan{
		$result = $this->collection->findOne(['lowerName' => strtolower($name)]);
		return $result instanceof BSONDocument ? Clan::fromBsonDocument($result) : null;
	}

	public function getClanByTag(string $tag) : ?Clan{
		$result = $this->collection->findOne(['lowerTag' => strtolower($tag)]);
		return $result instanceof BSONDocument ? Clan::fromBsonDocument($result) : null;
	}

	public function getClanById(ObjectId $id) : ?Clan{
		$result = $this->collection->findOne(['_id' => $id]);
		return $result instanceof BSONDocument ? Clan::fromBsonDocument($result) : null;
	}

	public function createClan(CollapsePlayer $leader, string $name, string $tag) : ?ClanError{
		if($this->getClanByName($name) !== null){
			return ClanError::AlreadyHasClanWithName;
		}

		if($this->getClanByTag($tag) !== null){
			return ClanError::AlreadyHasClanWithTag;
		}

		$clan = Clan::create($leader, $name, $tag);

		$start = microtime(true);
		MongoWrapper::push(new InsertOneOperation(
			$this->collection->getDatabaseName(),
			$this->collection->getCollectionName(),
			$clan->export()
		))->onResolve(
			function(?ObjectId $insertedId) use ($clan, $leader, $start) : void{
				$this->logger->notice("Clan {$clan->getName()} created in " . round(microtime(true) - $start, 5) . "ms");
				$clan->onInsert($insertedId);
				$leader->getProfile()?->setClanId($clan->getId());
				$leader->getProfile()?->setClanRole(ClanRole::LEADER);
				$leader->sendMessage($leader->getProfile()->getClanId()->__toString());
				(new ClanCreatedEvent($clan))->call();
			}
		);
		return null;
	}

	public function disbandClan(Clan $clan) : void{
		foreach($clan->getMembers() as $member){
			$profile = Practice::getPlayerByXuid($member->getXuid())?->getProfile() ?? Practice::getInstance()->getProfileManager()->getProfileByXuid($member->getXuid());
			if($profile === null){
				continue;
			}

			$profile->setClanRole(null);
			$profile->setClanId(null);
		}

		$this->collection->deleteOne(['_id' => $clan->getId()]);
		(new ClanDisbandedEvent($clan))->call();
	}

	public function saveClan(Clan $clan) : void{
		$start = microtime(true);
		MongoWrapper::push(
			new ReplaceOneOperation(
				$this->collection->getDatabaseName(),
				$this->collection->getCollectionName(),
				['_id' => $clan->getId()],
				$clan->export()
			)
		)->onResolve(
			function() use ($clan, $start) : void{
				$this->logger->debug("Clan {$clan->getName()} saved in " . round(microtime(true) - $start, 5) . "ms");
			}
		);
	}

	public function getTopClans(int $limit = 10) : array{
		$cursor = $this->collection->find(
			[],
			[
				'sort' => ['kills' => -1],
				'limit' => $limit
			]
		);

		$clans = [];
		foreach($cursor as $document){
			$clans[] = Clan::fromBsonDocument($document);
		}
		return $clans;
	}
}

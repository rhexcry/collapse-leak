<?php

declare(strict_types=1);

namespace collapse\feature\concrete\quest;

use collapse\mongo\MongoWrapper;
use collapse\player\profile\Profile;
use collapse\Practice;
use MongoDB\Collection;
use MongoDB\Model\BSONDocument;

final class QuestManager{
	private Collection $questsCollection;
	private Collection $progressCollection;

	private \PrefixedLogger $logger;

	private bool $loadedInCache = false;
	private array $cachedQuestsMap = [];

	public function __construct(){
		$this->questsCollection = MongoWrapper::getClient()->selectCollection(Practice::getDatabaseName(), 'quests');
		$this->progressCollection = MongoWrapper::getClient()->selectCollection(Practice::getDatabaseName(), 'quest_progress');

		$this->logger = new \PrefixedLogger(Practice::getInstance()->getLogger(), 'Quests');
	}

	public function loadInCache() : void{
		$this->logger->debug('Loading quests into cache...');

		$cursor = $this->questsCollection->find();
		$count = 0;

		foreach($cursor as $document){
			$quest = Quest::fromBSON($document);
			$this->cachedQuestsMap[$quest->getId()] = $quest;
			$count++;
		}

		$this->loadedInCache = true;

		$this->logger->debug('Loaded ' . $count . ' quests into cache.');
	}

	public function getAllQuests() : array{
		if($this->loadedInCache){
			return $this->cachedQuestsMap;
		}

		$quests = [];

		$cursor = $this->questsCollection->find();
		foreach($cursor as $document){
			$quest = Quest::fromBSON($document);
			$quests[$quest->getId()] = $quest;
		}

		return $quests;
	}

	public function registerQuest(Quest $quest) : void{
		$this->questsCollection->updateOne(
			['id' => $quest->getId()],
			['$set' => $quest->serialize()],
			['upsert' => true]
		);

		$this->logger->debug('Registered new quest: ' . $quest->getId());
	}

	public function getQuest(string $id) : ?Quest{
		if($this->loadedInCache){
			return $this->cachedQuestsMap[$id] ?? null;
		}

		/** @var BSONDocument $doc */
		$doc = $this->questsCollection->findOne(['id' => $id]);
		return $doc ? Quest::fromBSON($doc) : null;
	}

	public function getPlayerProgress(Profile $profile, string $questId) : ?QuestProgress{
		/** @var BSONDocument $doc */
		$doc = $this->progressCollection->findOne([
			'player_xuid' => $profile->getXuid(),
			'quest_id' => $questId
		]);
		return $doc ? QuestProgress::fromBSON($doc) : new QuestProgress($profile->getXuid(), $questId);
	}

	public function updateProgress(QuestProgress $progress) : void{
		$this->progressCollection->updateOne(
			[
				'player_xuid' => $progress->getPlayerXuid(),
				'quest_id' => $progress->getQuestId()
			],
			['$set' => $progress->serialize()],
			['upsert' => true]
		);
	}

	public function onQuestCompleted(QuestProgress $progress) : void{
		$quest = $this->getQuest($progress->getQuestId());
		$profile = Practice::getInstance()->getProfileManager()->getProfileByXuid($progress->getPlayerXuid());
		$progress->setCompleted();

		$this->updateProgress($progress);

		if(($player = $profile->getPlayer()) !== null){
			$player->sendToastNotification($player->getProfile()->getTranslator()->translate($quest->getName()), 'completed');
		}
	}
}

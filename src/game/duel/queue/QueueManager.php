<?php

declare(strict_types=1);

namespace collapse\game\duel\queue;

use collapse\game\duel\DuelManager;
use collapse\game\duel\item\DuelItems;
use collapse\game\duel\queue\entry\QueueEntry;
use collapse\game\duel\queue\entry\SoloQueueEntry;
use collapse\game\duel\queue\event\PlayerJoinQueueEvent;
use collapse\game\duel\queue\event\PlayerLeaveQueueEvent;
use collapse\game\duel\types\DuelMode;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use function array_filter;

final class QueueManager{

	/** @var UnrankedQueue[] */
	private array $soloUnrankedQueues = [];

	/** @var RankedQueue[] */
	private array $soloRankedQueues = [];

	/** @var SoloQueueEntry[] */
	private array $soloQueueEntries = [];

	public function __construct(
		private readonly DuelManager $duelManager
	){
		foreach(DuelMode::cases() as $mode){
			$this->soloUnrankedQueues[$mode->value] = new UnrankedQueue($mode, $this);
		}
		foreach(DuelMode::ranked() as $mode){
			$this->soloRankedQueues[$mode->value] = new RankedQueue($mode, $this);
		}
		$this->duelManager->getPlugin()->getServer()->getPluginManager()->registerEvents(new QueueListener($this), $this->duelManager->getPlugin());
	}

	public function getDuelManager() : DuelManager{
		return $this->duelManager;
	}

	public function getSoloUnrankedQueue(DuelMode $mode) : ?UnrankedQueue{
		return $this->soloUnrankedQueues[$mode->value] ?? null;
	}

	public function getSoloRankedQueue(DuelMode $mode) : ?RankedQueue{
		return $this->soloRankedQueues[$mode->value] ?? null;
	}

	public function getSoloUnrankedInQueue(?DuelMode $mode = null) : int{
		$inQueue = 0;
		foreach(array_filter($this->soloUnrankedQueues, static function(UnrankedQueue $queue) use ($mode) : bool{
			if($mode !== null && $queue->getMode() !== $mode){
				return false;
			}
			return true;
		}) as $queue){
			$inQueue += $queue->getCount();
		}
		return $inQueue;
	}

	public function getSoloRankedInQueue(?DuelMode $mode = null) : int{
		$inQueue = 0;
		foreach(array_filter($this->soloRankedQueues, static function(RankedQueue $queue) use ($mode) : bool{
			if($mode !== null && $queue->getMode() !== $mode){
				return false;
			}
			return true;
		}) as $queue){
			$inQueue += $queue->getCount();
		}
		return $inQueue;
	}

	private function getSoloQueueEntry(CollapsePlayer $player) : ?SoloQueueEntry{
		return $this->soloQueueEntries[$player->getName()] ?? null;
	}

	public function isInQueue(CollapsePlayer $player) : bool{
		return $this->getSoloQueueEntry($player) !== null;
	}

	private function onPlayerEnteredQueue(CollapsePlayer $player, QueueEntry $entry) : void{
		$player->setScoreboard(new QueueScoreboard($player, $entry));
		$player->getInventory()->clearAll();
		$player->getInventory()->setItem(8, DuelItems::LEAVE_QUEUE()->translate($player));
	}

	public function joinSoloQueue(CollapsePlayer $player, DuelMode $mode, bool $ranked = false) : void{
		if($this->isInQueue($player)){
			return;
		}
		if(!$this->duelManager->getMapPool()->isAnyMapsExists($mode)){
			$player->sendTranslatedMessage(CollapseTranslationFactory::duels_no_maps());
			return;
		}
		$queue = $ranked ? $this->getSoloRankedQueue($mode) : $this->getSoloUnrankedQueue($mode);
		$queue->add($entry = new SoloQueueEntry($queue, $player));

		$event = new PlayerJoinQueueEvent($queue, $player);
		$event->call();
		if($event->isCancelled()){
			return;
		}

		$this->soloQueueEntries[$player->getName()] = $entry;
		$this->onPlayerEnteredQueue($player, $entry);
		$queue->onUpdate();
	}

	public function removeFromSoloQueue(CollapsePlayer $player) : void{
		$entry = $this->getSoloQueueEntry($player);
		if($entry === null){
			return;
		}

		$entry->getQueue()->remove($entry);
		unset($this->soloQueueEntries[$player->getName()]);
	}

	public function onPlayerLeaveQueue(CollapsePlayer $player) : void{
		$entry = $this->getSoloQueueEntry($player);
		if($entry === null){
			return;
		}
		$this->removeFromSoloQueue($player);
		(new PlayerLeaveQueueEvent($entry->getQueue(), $player))->call();
	}
}

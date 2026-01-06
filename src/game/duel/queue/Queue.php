<?php

declare(strict_types=1);

namespace collapse\game\duel\queue;

use collapse\game\duel\queue\entry\QueueEntry;
use collapse\game\duel\types\DuelMode;
use collapse\player\CollapsePlayer;

abstract class Queue{

	/** @var \SplObjectStorage<QueueEntry> */
	protected \SplObjectStorage $entries;

	public function __construct(
		protected DuelMode $mode,
		protected QueueManager $queueManager
	){
		$this->entries = new \SplObjectStorage();
	}

	final public function getMode() : DuelMode{
		return $this->mode;
	}

	final public function getQueueManager() : QueueManager{
		return $this->queueManager;
	}

	abstract public function onUpdate() : void;

	/**
	 * @param CollapsePlayer[] $members
	 */
	abstract protected function onMembersFound(array $members) : void;

	final public function add(QueueEntry $entry) : void{
		$this->entries->attach($entry);
	}

	final public function remove(QueueEntry $entry) : void{
		if($this->entries->contains($entry)){
			$this->entries->detach($entry);
		}
	}

	final public function getCount() : int{
		return $this->entries->count();
	}
}

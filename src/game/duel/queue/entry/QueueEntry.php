<?php

declare(strict_types=1);

namespace collapse\game\duel\queue\entry;

use collapse\game\duel\queue\Queue;
use collapse\player\CollapsePlayer;
use function time;

abstract readonly class QueueEntry{

	private int $timeJoined;

	public function __construct(
		private Queue $queue
	){
		$this->timeJoined = time();
	}

	final public function getQueue() : Queue{
		return $this->queue;
	}

	/**
	 * @return CollapsePlayer|CollapsePlayer[]
	 */
	abstract public function getMembers() : CollapsePlayer|array;

	final public function getWaitSeconds() : int{
		return time() - $this->timeJoined;
	}
}

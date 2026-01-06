<?php

declare(strict_types=1);

namespace collapse\game\duel\queue\event;

use collapse\game\duel\queue\Queue;
use pocketmine\event\Event;

abstract class QueueEvent extends Event{

	public function __construct(
		protected readonly Queue $queue
	){}

	public function getQueue() : Queue{
		return $this->queue;
	}
}

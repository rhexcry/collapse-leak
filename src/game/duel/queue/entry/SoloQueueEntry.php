<?php

declare(strict_types=1);

namespace collapse\game\duel\queue\entry;

use collapse\game\duel\queue\Queue;
use collapse\player\CollapsePlayer;

final readonly class SoloQueueEntry extends QueueEntry{

	public function __construct(
		Queue $queue,
		private CollapsePlayer $player
	){
		parent::__construct($queue);
	}

	public function getMembers() : CollapsePlayer{
		return $this->player;
	}
}

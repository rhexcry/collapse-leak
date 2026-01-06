<?php

declare(strict_types=1);

namespace collapse\game\duel\queue\event;

use collapse\game\duel\queue\Queue;
use collapse\player\CollapsePlayer;
use pocketmine\event\CancellableTrait;

final class PlayerJoinQueueEvent extends QueueEvent{
	use CancellableTrait;

	public function __construct(
		Queue $queue,
		private readonly CollapsePlayer $player
	){
		parent::__construct($queue);
	}

	public function getPlayer() : CollapsePlayer{
		return $this->player;
	}
}

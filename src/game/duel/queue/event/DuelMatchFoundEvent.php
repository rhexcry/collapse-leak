<?php

declare(strict_types=1);

namespace collapse\game\duel\queue\event;

use collapse\game\duel\queue\Queue;
use collapse\player\CollapsePlayer;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class DuelMatchFoundEvent extends QueueEvent implements Cancellable{
	use CancellableTrait;

	/**
	 * @param CollapsePlayer[] $members
	 */
	public function __construct(
		private readonly array   $members,
		Queue $queue
	){
		parent::__construct($queue);
	}

	public function getMembers() : array{
		return $this->members;
	}
}

<?php

declare(strict_types=1);

namespace collapse\system\observe\event;

use collapse\player\CollapsePlayer;
use pocketmine\event\Event;

abstract class ObserveEvent extends Event{

	public function __construct(
		private readonly CollapsePlayer $observer,
		private readonly ?CollapsePlayer $target
	){}

	public function getObserver() : CollapsePlayer{
		return $this->observer;
	}

	public function getTarget() : ?CollapsePlayer{
		return $this->target;
	}
}

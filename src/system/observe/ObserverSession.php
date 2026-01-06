<?php

declare(strict_types=1);

namespace collapse\system\observe;

use collapse\player\CollapsePlayer;

readonly class ObserverSession{

	public function __construct(
		private CollapsePlayer $observer,
		private ?CollapsePlayer $target
	){}

	public function getObserver() : CollapsePlayer{
		return $this->observer;
	}

	public function getTarget() : ?CollapsePlayer{
		return $this->target;
	}

	public function update() : void{
		$this->observer->teleport($this->target->getPosition());
	}
}

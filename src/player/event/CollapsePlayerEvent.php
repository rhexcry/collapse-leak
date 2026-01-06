<?php

declare(strict_types=1);

namespace collapse\player\event;

use collapse\player\CollapsePlayer;
use pocketmine\event\Event;

abstract class CollapsePlayerEvent extends Event{

	public function __construct(
		protected readonly CollapsePlayer $player
	){}

	public function getPlayer() : CollapsePlayer{
		return $this->player;
	}
}

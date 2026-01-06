<?php

declare(strict_types=1);

namespace collapse\game\event;

use collapse\player\CollapsePlayer;
use pocketmine\event\Event;

abstract class CombatEvent extends Event{

	public function __construct(
		private readonly CollapsePlayer $player,
		private readonly CollapsePlayer $opponent
	){}

	public function getPlayer() : CollapsePlayer{
		return $this->player;
	}

	public function getOpponent() : CollapsePlayer{
		return $this->opponent;
	}
}

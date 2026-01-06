<?php

declare(strict_types=1);

namespace collapse\game\event;

use collapse\game\Game;
use pocketmine\event\Event;

abstract class GameEvent extends Event{

	public function __construct(
		private readonly Game $game
	){}

	public function getGame() : Game{
		return $this->game;
	}
}

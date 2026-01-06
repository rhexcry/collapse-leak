<?php

declare(strict_types=1);

namespace collapse\game\respawn;

use collapse\game\Game;
use collapse\player\CollapsePlayer;
use pocketmine\event\Event;

final class PlayerRespawnGameEvent extends Event{

	public function __construct(
		private readonly CollapsePlayer $player,
		private readonly Game $game
	){}

	public function getPlayer() : CollapsePlayer{
		return $this->player;
	}

	public function getGame() : Game{
		return $this->game;
	}
}

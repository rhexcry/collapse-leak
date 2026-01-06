<?php

declare(strict_types=1);

namespace collapse\game\respawn;

use collapse\player\CollapsePlayer;
use pocketmine\event\Event;

final class PlayerRespawnUpdateEvent extends Event{

	public function __construct(
		private readonly CollapsePlayer $player,
		private readonly int $countdown
	){}

	public function getPlayer() : CollapsePlayer{
		return $this->player;
	}

	public function getCountdown() : int{
		return $this->countdown;
	}
}

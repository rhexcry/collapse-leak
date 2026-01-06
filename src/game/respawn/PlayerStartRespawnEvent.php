<?php

declare(strict_types=1);

namespace collapse\game\respawn;

use collapse\player\CollapsePlayer;
use pocketmine\event\Event;

final class PlayerStartRespawnEvent extends Event{

	public function __construct(
		private readonly CollapsePlayer $player
	){}

	public function getPlayer() : CollapsePlayer{
		return $this->player;
	}
}

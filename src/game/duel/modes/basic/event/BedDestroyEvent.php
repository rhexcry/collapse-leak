<?php

declare(strict_types=1);

namespace collapse\game\duel\modes\basic\event;

use collapse\game\teams\Team;
use collapse\player\CollapsePlayer;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;

final class BedDestroyEvent extends Event{
	use CancellableTrait;

	public function __construct(
		private readonly Team $team,
		private readonly CollapsePlayer $player
	){}

	public function getTeam() : Team{
		return $this->team;
	}

	public function getPlayer() : CollapsePlayer{
		return $this->player;
	}
}

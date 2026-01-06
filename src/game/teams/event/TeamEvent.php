<?php

declare(strict_types=1);

namespace collapse\game\teams\event;

use collapse\game\teams\Team;
use pocketmine\event\Event;

abstract class TeamEvent extends Event{

	public function __construct(
		protected readonly Team $team
	){}

	public function getTeam() : Team{
		return $this->team;
	}
}

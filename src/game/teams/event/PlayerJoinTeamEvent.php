<?php

declare(strict_types=1);

namespace collapse\game\teams\event;

use collapse\game\teams\Team;
use collapse\player\CollapsePlayer;

final class PlayerJoinTeamEvent extends TeamEvent{

	public function __construct(
		Team $team,
		private readonly CollapsePlayer $player
	){
		parent::__construct($team);
	}

	public function getPlayer() : CollapsePlayer{
		return $this->player;
	}
}

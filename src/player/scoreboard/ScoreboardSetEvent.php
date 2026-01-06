<?php

declare(strict_types=1);

namespace collapse\player\scoreboard;

use collapse\player\CollapsePlayer;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;

final class ScoreboardSetEvent extends Event{
	use CancellableTrait;

	public function __construct(
		private readonly CollapsePlayer $player,
		private readonly ?Scoreboard $scoreboard
	){}

	public function getPlayer() : CollapsePlayer{
		return $this->player;
	}

	public function getScoreboard() : ?Scoreboard{
		return $this->scoreboard;
	}
}

<?php

declare(strict_types=1);

namespace collapse\game\duel\phase;

use collapse\game\duel\Duel;
use collapse\player\CollapsePlayer;
use collapse\player\scoreboard\CollapseScoreboard;

abstract class PhaseScoreboard extends CollapseScoreboard{

	public function __construct(
		CollapsePlayer $player,
		protected readonly Duel $duel,
		protected readonly Phase $phase
	){
		parent::__construct($player);
	}
}

<?php

declare(strict_types=1);

namespace collapse\player\scoreboard;

use collapse\resourcepack\CollapseUI;

abstract class CollapseScoreboard extends Scoreboard{

	final protected function title() : string{
		return CollapseUI::SCOREBOARD_LOGO;
	}
}

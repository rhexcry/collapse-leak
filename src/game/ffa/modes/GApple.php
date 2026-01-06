<?php

declare(strict_types=1);

namespace collapse\game\ffa\modes;

use collapse\game\ffa\FreeForAllArena;

final class GApple extends FreeForAllArena{

	public function isAntiInterrupt() : bool{
		return true;
	}

	public function isHidePlayersInCombat() : bool{
		return true;
	}

	public function isStatisticsEnabled() : bool{
		return true;
	}

	public function isCombat() : bool{
		return true;
	}
}

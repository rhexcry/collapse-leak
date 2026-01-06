<?php

declare(strict_types=1);

namespace collapse\game\ffa\modes\resistance;

use collapse\game\ffa\FreeForAllArena;
use collapse\utils\EventUtils;

final class Resistance extends FreeForAllArena{

	private const int DAMAGE_Y = -70;

	protected function setUp() : void{
		EventUtils::registerListenerOnce(new ResistanceListener());
		$this->setDamageY(self::DAMAGE_Y);
	}

	public function isAntiInterrupt() : bool{
		return true;
	}

	public function isCombat() : bool{
		return true;
	}

	public function isHidePlayersInCombat() : bool{
		return true;
	}

	public function isDamageDisabled() : bool{
		return true;
	}

	public function isStatisticsEnabled() : bool{
		return true;
	}
}
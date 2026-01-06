<?php

declare(strict_types=1);

namespace collapse\game\ffa\modes\sumo;

use collapse\game\ffa\FreeForAllArena;
use collapse\utils\EventUtils;

final class Sumo extends FreeForAllArena{

	protected function setUp() : void{
		EventUtils::registerListenerOnce(new SumoListener());
	}

	public function isDamageDisabled() : bool{
		return true;
	}

	public function isCombat() : bool{
		return true;
	}

	public function isAntiInterrupt() : bool{
		return true;
	}
}

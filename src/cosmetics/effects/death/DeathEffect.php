<?php

declare(strict_types=1);

namespace collapse\cosmetics\effects\death;

use collapse\player\CollapsePlayer;

abstract readonly class DeathEffect{

	public function __construct(
		protected CollapsePlayer $player,
		protected CollapsePlayer $killer
	){
		$this->spawn();
	}

	abstract protected function spawn() : void;
}

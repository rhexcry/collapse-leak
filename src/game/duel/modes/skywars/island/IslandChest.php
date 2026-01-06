<?php

declare(strict_types=1);

namespace collapse\game\duel\modes\skywars\island;

use pocketmine\world\Position;

final class IslandChest{

	public function __construct(
		private Position $position,
		private bool     $isLarge = false
	){}

	public function getPosition() : Position{
		return $this->position;
	}

	public function isLarge() : bool{
		return $this->isLarge;
	}
}
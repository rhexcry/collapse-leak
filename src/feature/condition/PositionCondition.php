<?php

declare(strict_types=1);

namespace collapse\feature\condition;

use collapse\player\CollapsePlayer;
use pocketmine\math\Vector3;

final readonly class PositionCondition implements ICondition{

	public function __construct(
		private Vector3 $position,
		private float $radius
	){
	}

	public function isMet(CollapsePlayer $player, mixed $data) : bool{
		return $player->getPosition()->distance($this->position) <= $this->radius;
	}
}

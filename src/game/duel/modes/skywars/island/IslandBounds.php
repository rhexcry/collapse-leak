<?php

declare(strict_types=1);

namespace collapse\game\duel\modes\skywars\island;

use pocketmine\math\Vector3;

final readonly class IslandBounds{

	public function __construct(
		private Vector3 $min,
		private Vector3 $max
	){
		$this->validateBounds();
	}

	private function validateBounds() : void{
		if($this->min->x > $this->max->x ||
			$this->min->y > $this->max->y ||
			$this->min->z > $this->max->z){
			throw new \InvalidArgumentException('Invalid island bounds');
		}
	}

	public function contains(Vector3 $point) : bool{
		return $point->x >= $this->min->x && $point->x <= $this->max->x &&
			$point->y >= $this->min->y && $point->y <= $this->max->y &&
			$point->z >= $this->min->z && $point->z <= $this->max->z;
	}

	public function getCenter() : Vector3{
		return new Vector3(
			($this->min->x + $this->max->x) / 2,
			($this->min->y + $this->max->y) / 2,
			($this->min->z + $this->max->z) / 2
		);
	}

	public function getMin() : Vector3{
		return $this->min;
	}

	public function getMax() : Vector3{
		return $this->max;
	}
}
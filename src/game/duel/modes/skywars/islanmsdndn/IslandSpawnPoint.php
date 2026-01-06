<?php

declare(strict_types=1);

namespace collapse\game\duel\modes\skywars\islanmsdndn;

use pocketmine\math\Vector3;
use pocketmine\world\Position;
use pocketmine\world\World;

final readonly class IslandSpawnPoint{

	public function __construct(
		private Vector3 $position,
		private float   $yaw = 0.0,
		private float   $pitch = 0.0
	){}

	public function getPosition() : Vector3{
		return $this->position;
	}

	public function getYaw() : float{
		return $this->yaw;
	}

	public function getPitch() : float{
		return $this->pitch;
	}

	public function toPosition(World $world) : Position{
		return Position::fromObject($this->position, $world);
	}
}
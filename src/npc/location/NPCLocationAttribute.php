<?php

declare(strict_types=1);

namespace collapse\npc\location;

#[\Attribute(\Attribute::TARGET_CLASS_CONSTANT)]
class NPCLocationAttribute{

	public function __construct(
		public float $x,
		public float $y,
		public float $z,
		public string $world,
		public float $yaw = 0.0,
		public float $pitch = 0.0
	){}
}

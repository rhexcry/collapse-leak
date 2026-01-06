<?php

declare(strict_types=1);

namespace collapse\world\sound;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\world\sound\Sound;

final readonly class MinecraftSound implements Sound{

	public function __construct(
		private string $soundName,
		private float $volume = 1.0,
		private float $pitch = 1.0
	){}

	public function encode(Vector3 $pos) : array{
		return [PlaySoundPacket::create($this->soundName, $pos->x, $pos->y, $pos->z, $this->volume, $this->pitch)];
	}
}

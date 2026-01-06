<?php

declare(strict_types=1);

namespace collapse\entity\animation;

use collapse\entity\FishingHook;
use pocketmine\entity\animation\Animation;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\types\ActorEvent;

final readonly class FishHookTeaseAnimation implements Animation{

	public function __construct(
		private FishingHook $fishingHook
	){}

	public function encode() : array{
		return [
			ActorEventPacket::create($this->fishingHook->getId(), ActorEvent::FISH_HOOK_TEASE, 0)
		];
	}
}

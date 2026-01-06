<?php

declare(strict_types=1);

namespace collapse\lobby\npc\animation;

use collapse\npc\animation\NPCAnimation;
use pocketmine\network\mcpe\protocol\AnimateEntityPacket;

final readonly class FreeForAllAnimation implements NPCAnimation{

	public function __construct(private int $runtimeId){}

	public function encode() : AnimateEntityPacket{
		return AnimateEntityPacket::create(
			'animation.ffa',
			'',
			'',
			0,
			'',
			0,
			[$this->runtimeId]
		);
	}
}

<?php

declare(strict_types=1);

namespace collapse\npc\animation;

use pocketmine\network\mcpe\protocol\AnimateEntityPacket;

interface NPCAnimation{

	public function encode() : AnimateEntityPacket;
}

<?php

declare(strict_types=1);

namespace collapse\lobby\npc;

use collapse\npc\CollapseNPC;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;

abstract class LobbyNPC extends CollapseNPC{

	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
		$this->setNameTagAlwaysVisible();
	}

	public function onCollideWithPlayer(Player $player) : void{
		$deltaX = $player->getLocation()->getX() - $this->location->x;
		$deltaZ = $player->getLocation()->getZ() - $this->location->z;
		$player->knockBack($deltaX, $deltaZ);
	}
}

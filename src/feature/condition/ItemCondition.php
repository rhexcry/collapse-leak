<?php

declare(strict_types=1);

namespace collapse\feature\condition;

use collapse\player\CollapsePlayer;
use pocketmine\item\Item;

final readonly class ItemCondition implements ICondition{

	public function __construct(
		private Item $requiredItem
	){
	}

	public function isMet(CollapsePlayer $player, mixed $data) : bool{
		return $player->getInventory()->contains($this->requiredItem);
	}
}

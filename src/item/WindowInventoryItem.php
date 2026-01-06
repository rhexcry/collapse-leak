<?php

declare(strict_types=1);

namespace collapse\item;

use collapse\player\CollapsePlayer;
use pocketmine\inventory\Inventory;

interface WindowInventoryItem{

	public function onClick(CollapsePlayer $player, Inventory $inventory) : void;
}

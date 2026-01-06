<?php

declare(strict_types=1);

namespace collapse\item;

use collapse\player\CollapsePlayer;

interface InventoryItem{

	public function onUse(CollapsePlayer $player) : void;
}

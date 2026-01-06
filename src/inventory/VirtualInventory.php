<?php

declare(strict_types=1);

namespace collapse\inventory;

use collapse\player\CollapsePlayer;

interface VirtualInventory{

	public function open(CollapsePlayer $player) : void;
}

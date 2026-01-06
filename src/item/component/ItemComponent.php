<?php

declare(strict_types=1);

namespace collapse\item\component;

use pocketmine\nbt\tag\CompoundTag;

abstract readonly class ItemComponent{

	abstract public function write(CompoundTag $nbt) : void;
}

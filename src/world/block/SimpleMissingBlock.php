<?php

declare(strict_types=1);

namespace collapse\world\block;

use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\BlockTypeInfo;

final class SimpleMissingBlock extends Block{

	public static function create(string $name) : self{
		return new self(new BlockIdentifier(BlockTypeIds::newId()), $name, new BlockTypeInfo(BlockBreakInfo::instant()));
	}
}

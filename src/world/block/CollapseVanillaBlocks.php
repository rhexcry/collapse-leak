<?php

declare(strict_types=1);

namespace collapse\world\block;

use pocketmine\block\Block;
use pocketmine\utils\CloningRegistryTrait;

final class CollapseVanillaBlocks{
	use CloningRegistryTrait;

	protected static function register(string $name, Block $block) : void{
		self::_registryRegister($name, $block);
	}

	protected static function setup() : void{

	}
}

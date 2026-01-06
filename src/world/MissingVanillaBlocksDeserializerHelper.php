<?php

declare(strict_types=1);

namespace collapse\world;

use pocketmine\block\Block;
use pocketmine\data\bedrock\block\convert\BlockStateReader;
use function array_keys;

final readonly class MissingVanillaBlocksDeserializerHelper{

	public static function decodeFullIgnored(Block $block, BlockStateReader $in) : Block{
		foreach(((function() : array{
			return array_keys($this->unusedStates);
		})->bindTo($in, $in))() as $state){
			$in->ignored($state);
		}
		return $block;
	}
}

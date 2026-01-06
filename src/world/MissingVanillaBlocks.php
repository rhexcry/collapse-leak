<?php

declare(strict_types=1);

namespace collapse\world;

use collapse\world\block\SimpleMissingBlock;
use pocketmine\block\Block;
use pocketmine\block\RuntimeBlockStateRegistry;
use pocketmine\data\bedrock\block\BlockTypeNames;
use pocketmine\data\bedrock\block\convert\BlockStateReader;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;
use pocketmine\item\StringToItemParser;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\world\format\io\GlobalBlockStateHandlers;

final readonly class MissingVanillaBlocks{

	public static function registerBlocks() : void{
		self::registerSimpleBlock(BlockTypeNames::SCAFFOLDING, SimpleMissingBlock::create('scaffolding'), ['scaffolding']);
		self::registerSimpleBlock(BlockTypeNames::COMPOSTER, SimpleMissingBlock::create('composter'), ['composter']);
		self::registerSimpleBlock(BlockTypeNames::BAMBOO_STAIRS, SimpleMissingBlock::create('bamboo_stairs'), ['bamboo_stairs']);
		self::registerSimpleBlock(BlockTypeNames::BAMBOO_MOSAIC_SLAB, SimpleMissingBlock::create('bamboo_mosaic_slab'), ['bamboo_mosaic_slab']);
		self::registerSimpleBlock(BlockTypeNames::BAMBOO_PRESSURE_PLATE, SimpleMissingBlock::create('bamboo_pressure_plate'), ['bamboo_pressure_plate']);
		self::registerSimpleBlock(BlockTypeNames::BAMBOO_BUTTON, SimpleMissingBlock::create('bamboo_button'), ['bamboo_button']);
		self::registerSimpleBlock(BlockTypeNames::BEEHIVE, SimpleMissingBlock::create('beehive'), ['beehive']);
		self::registerSimpleBlock(BlockTypeNames::PISTON, SimpleMissingBlock::create('piston'), ['piston']);
		self::registerSimpleBlock(BlockTypeNames::DISPENSER, SimpleMissingBlock::create('dispenser'), ['dispenser']);
		self::registerSimpleBlock(BlockTypeNames::DECORATED_POT, SimpleMissingBlock::create('decorated_pot'), ['decorated_pot']);
		self::registerSimpleBlock(BlockTypeNames::AZALEA, SimpleMissingBlock::create('azalea'), ['azalea']);
		self::registerSimpleBlock(BlockTypeNames::CRIMSON_NYLIUM, SimpleMissingBlock::create('crimson_nylium'), ['crimson_nylium']);
		//self::registerSimpleBlock(BlockTypeNames::CRIMSON_HANGING_SIGN, SimpleMissingBlock::create('crimson_hanging_sign'), ['crimson_hanging_sign']);
		self::registerSimpleBlock(BlockTypeNames::GRINDSTONE, SimpleMissingBlock::create('grindstone'), ['grindstone']);
		self::registerSimpleBlock(BlockTypeNames::PISTON_ARM_COLLISION, SimpleMissingBlock::create('piston_arm_collision'), ['piston_arm_collision']);
		self::registerSimpleBlock(BlockTypeNames::MANGROVE_PROPAGULE, SimpleMissingBlock::create('mangrove_propagule'), ['mangrove_propagule']);
		//self::registerSimpleBlock(BlockTypeNames::SPRUCE_HANGING_SIGN, SimpleMissingBlock::create('spruce_hanging_sign'), ['spruce_hanging_sign']);
		//self::registerSimpleBlock(BlockTypeNames::WARPED_HANGING_SIGN, SimpleMissingBlock::create('warped_hanging_sign'), ['warped_hanging_sign']);
		self::registerSimpleBlock(BlockTypeNames::FLOWERING_AZALEA, SimpleMissingBlock::create('azalea'), ['flowering_azalea']);
		self::registerSimpleBlock(BlockTypeNames::CHERRY_SAPLING, SimpleMissingBlock::create('cherry_sapling'), ['cherry_sapling']);
		self::registerSimpleBlock(BlockTypeNames::BAMBOO_PLANKS, SimpleMissingBlock::create('bamboo_planks'), ['bamboo_planks']);
		//self::registerSimpleBlock(BlockTypeNames::BAMBOO_STAIRS, SimpleMissingBlock::create('bamboo_stairs'), ['bamboo_stairs']);
	}

	/**
	 * @param string[] $stringToItemParserNames
	 */
	private static function registerSimpleBlock(string $id, Block $block, array $stringToItemParserNames) : void{
		RuntimeBlockStateRegistry::getInstance()->register($block);

		GlobalBlockStateHandlers::getDeserializer()->map($id, fn(BlockStateReader $in) : Block => MissingVanillaBlocksDeserializerHelper::decodeFullIgnored($block, $in));

		$blockStateDictionary = TypeConverter::getInstance()->getBlockTranslator()->getBlockStateDictionary();
		$blockStateData = $blockStateDictionary->generateCurrentDataFromStateId($blockStateDictionary->lookupStateIdFromIdMeta($id, 0));
		$writer = new BlockStateWriter($id);
		(function(array $states) : void{
			$this->states = $states;
		})->bindTo($writer, $writer)->call($writer, $blockStateData->getStates());
		GlobalBlockStateHandlers::getSerializer()->map($block, fn() => $writer);

		foreach($stringToItemParserNames as $name){
			StringToItemParser::getInstance()->registerBlock($name, fn() => clone $block);
		}
	}
}

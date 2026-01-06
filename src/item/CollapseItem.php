<?php

declare(strict_types=1);

namespace collapse\item;

use collapse\player\CollapsePlayer;
use collapse\PracticeConstants;
use pocketmine\block\Block;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\lang\Translatable;
use pocketmine\world\format\io\GlobalItemDataHandlers;

abstract class CollapseItem extends Item implements TranslatableItem{

	private static array $blockSerializers = [];

	public function __construct(
		ItemIdentifier $identifier,
		protected readonly Translatable $translation,
		string $name = 'Unknown',
		protected readonly ?Block $block = null
	){
		parent::__construct($identifier, $name);
		$this->setLore([PracticeConstants::ITEM_LORE]);
		if($identifier->getTypeId() < 0 && !isset(CollapseItem::$blockSerializers[$this::class]) && $this->block !== null){
			$itemSerializer = GlobalItemDataHandlers::getSerializer();
			$itemSerializer->map($this, function() use ($itemSerializer) : SavedItemData{
				$block = $this->block;
				return ((function() use ($block) : SavedItemData{
					return $this->standardBlock($block);
				})->bindTo($itemSerializer, $itemSerializer))();
			});
			CollapseItem::$blockSerializers[$this::class] = true;
		}
	}

	public function translate(CollapsePlayer $player) : self{
		return $this->setCustomName($player->getProfile()->getTranslator()->translate($this->translation));
	}
}

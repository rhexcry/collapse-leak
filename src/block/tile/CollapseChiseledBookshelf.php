<?php

declare(strict_types=1);

namespace collapse\block\tile;

use pocketmine\block\tile\ChiseledBookshelf;
use pocketmine\block\tile\Container;
use pocketmine\data\bedrock\item\SavedItemStackData;
use pocketmine\data\SavedDataLoadingException;
use pocketmine\item\Item;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use ReflectionClass;

final class CollapseChiseledBookshelf extends ChiseledBookshelf{

	protected function loadItems(CompoundTag $tag) : void{
		if(($inventoryTag = $tag->getTag(Container::TAG_ITEMS)) instanceof ListTag && $inventoryTag->getTagType() === NBT::TAG_Compound){
			$inventory = $this->getRealInventory();
			$listeners = $inventory->getListeners()->toArray();
			$inventory->getListeners()->remove(...$listeners); //prevent any events being fired by initialization

			$newContents = [];
			/** @var CompoundTag $itemNBT */
			foreach($inventoryTag as $slot => $itemNBT){
				try{
					if($itemNBT->getTag('Count') === null){
						$itemNBT->setByte('Count', 1);
					}

					$count = $itemNBT->getByte(SavedItemStackData::TAG_COUNT);
					if($count === 0){
						continue;
					}
					$newContents[$slot] = Item::nbtDeserialize($itemNBT);
				}catch(SavedDataLoadingException $e){
					//TODO: not the best solution
					\GlobalLogger::get()->logException($e);
					continue;
				}
			}
			$inventory->setContents($newContents);

			$inventory->getListeners()->add(...$listeners);
		}

		if(($lockTag = $tag->getTag(Container::TAG_LOCK)) instanceof StringTag){
			$reflection = new ReflectionClass($this);
			$property = $reflection->getProperty('lock');
			$property->setAccessible(true);
			$property->setValue($this, $lockTag->getValue());
		}
	}
}

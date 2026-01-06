<?php

declare(strict_types=1);

namespace collapse\block\tile;

use pocketmine\block\tile\Campfire;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;

final class CollapseCampfire extends Campfire{
	public function readSaveData(CompoundTag $nbt) : void{
		$items = [];
		$listeners = $this->inventory->getListeners()->toArray();
		$this->inventory->getListeners()->remove(...$listeners); //prevent any events being fired by initialization

		$cookingTimes = [];
		foreach([
			[0, 'Item1', 'ItemTime1'],
			[1, 'Item2','ItemTime2'],
			[2, 'Item3', 'ItemTime3'],
			[3, 'Item4', 'ItemTime4'],
		] as [$slot, $itemTag, $cookingTimeTag]){
			$tag = $nbt->getTag($itemTag);
			if($tag instanceof CompoundTag){
				if($tag->getTag('Count') === null){
					$tag->setByte('Count', 1);
				}
				$items[$slot] = Item::nbtDeserialize($tag);
			}
			$tag = $nbt->getTag($cookingTimeTag);
			if($tag instanceof IntTag){
				$cookingTimes[$slot] = $tag->getValue();
			}
		}
		$this->setCookingTimes($cookingTimes);
		$this->inventory->setContents($items);
		$this->inventory->getListeners()->add(...$listeners);
	}
}

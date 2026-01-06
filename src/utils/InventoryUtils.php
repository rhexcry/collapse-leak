<?php

declare(strict_types=1);

namespace collapse\utils;

use pocketmine\inventory\Inventory;
use pocketmine\item\Item;

final readonly class InventoryUtils{

	private function __construct(){}

	/**
	 * @param Inventory|Item[] $inventory
	 */
	public static function countItems(Inventory|array $inventory, int $typeId) : int{
		$result = 0;
		foreach($inventory instanceof Inventory ? $inventory->getContents() : $inventory as $item){
			if($item->getTypeId() === $typeId){
				$result += $item->getCount();
			}
		}
		return $result;
	}
}

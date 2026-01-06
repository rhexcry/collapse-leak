<?php

declare(strict_types=1);

namespace collapse\utils;

use collapse\item\component\DisplayNameComponent;
use collapse\item\component\ItemComponents;
use collapse\item\component\ItemPropertiesComponent;
use collapse\item\ResourcePackItem;
use pocketmine\data\bedrock\item\SavedItemData;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\types\ItemTypeEntry;
use pocketmine\world\format\io\GlobalItemDataHandlers;
use function array_values;
use function str_replace;
use function strtolower;

final class ItemUtils{

	/** @var ItemTypeEntry[] */
	private static array $itemTypeEntries = [];

	private function __construct(){
	}

	public static function getItemTypeEntries() : array{
		return array_values(self::$itemTypeEntries);
	}

	public static function registerResourcePackItem(ResourcePackItem $item) : void{
		$itemSerializer = GlobalItemDataHandlers::getSerializer();
		$itemDeserializer = GlobalItemDataHandlers::getDeserializer();

		$cleanName = str_replace(' ', '_', strtolower($item->getName()));
		$name = 'minecraft:' . $cleanName;

		$itemSerializer->map($item, fn() => new SavedItemData($name));
		$itemDeserializer->map($name, fn() => clone $item);

		foreach(ProtocolInfo::ACCEPTED_PROTOCOL as $protocol){
			$itemTypeDictionary = TypeConverter::getInstance($protocol)->getItemTypeDictionary();

			$entries = &self::$itemTypeEntries;

			(function() use ($item, $cleanName, $name, &$entries) : void{
				$this->stringToIntMap[$name] = $item->getRuntimeId();
				$this->intToStringIdMap[$item->getRuntimeId()] = $name;

				$components = ItemComponents::create($item->getRuntimeId())
					->with(DisplayNameComponent::create($item->getName()))
					->with(ItemPropertiesComponent::create(
						$cleanName,
						$name
					));

				$item->addComponents($components);

				$entry = new ItemTypeEntry(
					$name,
					$item->getRuntimeId(),
					true,
					1,
					new CacheableNbt($components->toNbt())
				);

				$entries[$name] = $entry;
				$this->itemTypes[] = $entry;
			})->bindTo($itemTypeDictionary, $itemTypeDictionary)();
		}
	}
}
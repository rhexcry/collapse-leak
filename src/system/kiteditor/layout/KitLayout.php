<?php

declare(strict_types=1);

namespace collapse\system\kiteditor\layout;

use pocketmine\item\Item;
use pocketmine\utils\Utils;
use function count;

final readonly class KitLayout{

	/**
	 * @param array<int, array{id: int}> $contents
	 */
	public function __construct(
		private array $contents = [],
	){}

	/**
	 * @param Item[] $items
	 */
	public static function fromItems(array $items) : self{
		Utils::validateArrayValueType($items, function(mixed $value) : void{
			if(!$value instanceof Item){
				throw new \InvalidArgumentException('Value must be Item');
			}
		});

		$contents = [];
		foreach($items as $slot => $item){
			if(!$item->isNull()){
				$contents[$slot] = [
					'id' => $item->getTypeId()
				];
			}
		}

		return new self($contents);
	}

	/**
	 * @param array<array{id: int}> $data
	 */
	public static function fromData(array $data) : self{
		Utils::validateArrayValueType($data, function(mixed $value) : void{
			if(!is_array($value) || !isset($value['id'])){
				throw new \InvalidArgumentException('Value must be item data array');
			}
		});

		return new self($data);
	}

	public function equals(KitLayout $layout) : bool{
		if(count($this->contents) !== count($layout->getContents())){
			return false;
		}

		foreach($this->contents as $slot => $itemData){
			if(!isset($layout->getContents()[$slot])){
				return false;
			}

			$otherItemData = $layout->getContents()[$slot];
			if($itemData['id'] !== $otherItemData['id']){
				return false;
			}
		}

		return true;
	}

	public function getContents() : array{
		return $this->contents;
	}
}
<?php

declare(strict_types=1);

namespace collapse\game\duel\modes\skywars\loot;

use collapse\utils\WeightedRandom;
use pocketmine\item\Item;
use pocketmine\utils\Random;

final class LootPool{

	/** @var WeightedRandom<LootItem> */
	private WeightedRandom $weightedRandom;

	public function __construct(
		private readonly int $minItems,
		private readonly int $maxItems,
		LootItem    ...$lootItems
	){
		$this->weightedRandom = new WeightedRandom();
		foreach($lootItems as $item){
			$this->weightedRandom->add($item, $item->getWeight());
		}
	}

	/**
	 * @return Item[]
	 */
	public function generate(Random $random) : array{
		$count = $random->nextRange($this->minItems, $this->maxItems);
		$items = [];

		for($i = 0; $i < $count; $i++){
			$lootItem = $this->weightedRandom->get($random);
			$items[] = $lootItem->generate($random);
		}

		return $items;
	}
}
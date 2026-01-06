<?php

declare(strict_types=1);

namespace collapse\game\duel\modes\skywars\loot;

use pocketmine\item\Item;
use pocketmine\utils\Random;

final readonly class LootItem{

	public function __construct(
		private Item $item,
		private int  $minAmount = 1,
		private int  $maxAmount = 1,
		private int  $weight = 1
	){
		if($minAmount > $maxAmount){
			throw new \InvalidArgumentException('Min amount cannot be greater than max amount');
		}
	}

	public function generate(Random $random) : Item{
		$amount = $this->minAmount === $this->maxAmount
			? $this->minAmount
			: $random->nextRange($this->minAmount, $this->maxAmount);

		return $this->item->setCount($amount);
	}

	public function getWeight() : int{
		return $this->weight;
	}
}
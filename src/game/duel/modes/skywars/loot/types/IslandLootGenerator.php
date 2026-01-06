<?php

declare(strict_types=1);

namespace collapse\game\duel\modes\skywars\loot\types;

use collapse\game\duel\modes\skywars\loot\LootTableManager;
use collapse\game\duel\modes\skywars\SkyWarsChestType;
use pocketmine\block\tile\Chest;
use pocketmine\utils\Random;
use pocketmine\world\World;

final readonly class IslandLootGenerator{

	public function __construct(
		private LootTableManager $lootTableManager,
		private Random           $random
	){}

	public function generateIslandLoot(SkyWarsChestType $chestType, int $chestCount) : array{
		$chestContents = [];

		for($i = 0; $i < $chestCount; $i++){
			$chestContents[] = $this->lootTableManager->generateChestLoot(
				$chestType,
				$this->random
			);
		}

		return $chestContents;
	}

	public function fillChestsInWorld(World $world, array $chestPositions, SkyWarsChestType $chestType) : void{
		foreach($chestPositions as $position){
			$tile = $world->getTile($position);
			if($tile instanceof Chest){
				$loot = $this->lootTableManager->generateChestLoot($chestType, $this->random);
				$this->fillChest($tile, $loot);
			}
		}
	}

	private function fillChest(Chest $chest, array $items) : void{
		$inventory = $chest->getInventory();
		$slots = range(0, $inventory->getSize() - 1);
		shuffle($slots);

		foreach($items as $index => $item){
			if($index >= count($slots)) break;
			$inventory->setItem($slots[$index], $item);
		}
	}
}
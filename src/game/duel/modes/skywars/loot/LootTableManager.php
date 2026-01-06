<?php

declare(strict_types=1);

namespace collapse\game\duel\modes\skywars\loot;

use collapse\game\duel\modes\skywars\SkyWarsChestType;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\utils\Random;

final class LootTableManager{

	/** @var array<SkyWarsChestType, LootPool[]> */
	private array $chestLootPools = [];

	public function __construct(){
		$this->initializeLootTables();
	}

	private function initializeLootTables() : void{
		$this->chestLootPools[SkyWarsChestType::BASIC->value] = [
			new LootPool(3, 5,
				new LootItem(VanillaItems::STONE_SWORD(), 1, 1, 15),
				new LootItem(VanillaItems::WOODEN_SWORD(), 1, 1, 20),
				new LootItem(VanillaItems::BOW(), 1, 1, 10),
				new LootItem(VanillaItems::ARROW(), 5, 16, 25),
				new LootItem(VanillaItems::LEATHER_CAP(), 1, 1, 15),
				new LootItem(VanillaItems::LEATHER_TUNIC(), 1, 1, 15),
				new LootItem(VanillaItems::LEATHER_PANTS(), 1, 1, 15),
				new LootItem(VanillaItems::LEATHER_BOOTS(), 1, 1, 15),
				new LootItem(VanillaItems::STEAK(), 2, 4, 30),
				new LootItem(VanillaItems::GOLDEN_APPLE(), 1, 2, 8),
				new LootItem(VanillaBlocks::COAL()->asItem(), 2, 4, 20),
				new LootItem(VanillaBlocks::STONE()->asItem(), 16, 32, 25)
			)
		];

		$this->chestLootPools[SkyWarsChestType::MID->value] = [
			new LootPool(4, 6,
				new LootItem(VanillaItems::IRON_SWORD(), 1, 1, 20),
				new LootItem(VanillaItems::DIAMOND_SWORD(), 1, 1, 5),
				new LootItem(VanillaItems::BOW(), 1, 1, 15),
				new LootItem(VanillaItems::ARROW(), 8, 24, 30),
				new LootItem(VanillaItems::IRON_HELMET(), 1, 1, 12),
				new LootItem(VanillaItems::IRON_CHESTPLATE(), 1, 1, 12),
				new LootItem(VanillaItems::IRON_LEGGINGS(), 1, 1, 12),
				new LootItem(VanillaItems::IRON_BOOTS(), 1, 1, 12),
				new LootItem(VanillaItems::GOLDEN_APPLE(), 2, 4, 15),
				new LootItem(VanillaItems::ENCHANTED_GOLDEN_APPLE(), 1, 1, 3),
				new LootItem(VanillaItems::ENDER_PEARL(), 1, 2, 8),
				new LootItem(VanillaBlocks::TNT()->asItem(), 1, 3, 10),
				new LootItem(VanillaItems::WATER_BUCKET(), 1, 1, 5)
			)
		];

		$this->chestLootPools[SkyWarsChestType::OP->value] = [
			new LootPool(5, 7,
				new LootItem(VanillaItems::DIAMOND_SWORD(), 1, 1, 25),
				new LootItem(VanillaItems::DIAMOND_AXE(), 1, 1, 15),
				new LootItem(VanillaItems::BOW(), 1, 1, 20),
				new LootItem(VanillaItems::ARROW(), 16, 32, 35),
				new LootItem(VanillaItems::DIAMOND_HELMET(), 1, 1, 20),
				new LootItem(VanillaItems::DIAMOND_CHESTPLATE(), 1, 1, 20),
				new LootItem(VanillaItems::DIAMOND_LEGGINGS(), 1, 1, 20),
				new LootItem(VanillaItems::DIAMOND_BOOTS(), 1, 1, 20),
				new LootItem(VanillaItems::ENCHANTED_GOLDEN_APPLE(), 2, 4, 15),
				new LootItem(VanillaItems::ENDER_PEARL(), 2, 4, 20),
				new LootItem(VanillaItems::FIRE_CHARGE(), 4, 8, 15),
				new LootItem(VanillaItems::EXPERIENCE_BOTTLE(), 8, 16, 25)
			)
		];
	}

	/**
	 * @return Item[]
	 */
	public function generateChestLoot(SkyWarsChestType $chestType, Random $random) : array{
		$loot = [];
		$pools = $this->chestLootPools[$chestType->value] ?? [];

		foreach($pools as $pool){
			$loot = array_merge($loot, $pool->generate($random));
		}

		return $loot;
	}
}

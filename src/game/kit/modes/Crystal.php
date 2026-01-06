<?php

declare(strict_types=1);

namespace collapse\game\kit\modes;

use collapse\game\ffa\modes\crystal\item\CollapseEndCrystalItem;
use collapse\game\kit\KitCollection;
use collapse\item\default\CollapseEnderPearl;
use collapse\player\CollapsePlayer;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\PotionType;
use pocketmine\item\VanillaItems;

final class Crystal extends KitCollection {

	public function __construct() {
		$identifier = VanillaItems::ENDER_PEARL()->getTypeId();

		$armorEnchants = [
			new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 3),
			new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)
		];

		$armor = [
			VanillaItems::NETHERITE_HELMET()
				->addEnchantment($armorEnchants[0])
				->addEnchantment(new EnchantmentInstance(VanillaEnchantments::BLAST_PROTECTION(), 1))
				->addEnchantment($armorEnchants[1]),

			VanillaItems::NETHERITE_CHESTPLATE()
				->addEnchantment($armorEnchants[0])
				->addEnchantment(new EnchantmentInstance(VanillaEnchantments::BLAST_PROTECTION(), 2))
				->addEnchantment($armorEnchants[1]),

			VanillaItems::NETHERITE_LEGGINGS()
				->addEnchantment($armorEnchants[0])
				->addEnchantment(new EnchantmentInstance(VanillaEnchantments::BLAST_PROTECTION(), 4))
				->addEnchantment($armorEnchants[1]),

			VanillaItems::NETHERITE_BOOTS()
				->addEnchantment($armorEnchants[0])
				->addEnchantment(new EnchantmentInstance(VanillaEnchantments::BLAST_PROTECTION(), 1))
				->addEnchantment($armorEnchants[1])
		];

		$items = [
			VanillaItems::NETHERITE_SWORD()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 3)),
			(new CollapseEnderPearl(new ItemIdentifier($identifier)))->setCount(16),
			VanillaBlocks::OBSIDIAN()->asItem()->setCount(64),
			(new CollapseEndCrystalItem())->setCount(64),
			VanillaItems::GOLDEN_APPLE()->setCount(32),

			VanillaItems::TOTEM(),
			VanillaItems::TOTEM(),
			VanillaItems::TOTEM(),

			VanillaItems::NETHERITE_PICKAXE()
				->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING(), 2))
				->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 2)),

			17 => VanillaItems::SPLASH_POTION()->setType(PotionType::SWIFTNESS),
			19 => (new CollapseEnderPearl(new ItemIdentifier($identifier)))->setCount(16),
			20 => VanillaBlocks::OBSIDIAN()->asItem()->setCount(64),
			21 => (new CollapseEndCrystalItem())->setCount(64),
			23 => VanillaItems::TOTEM(),
			24 => VanillaItems::TOTEM(),
			25 => VanillaItems::TOTEM(),
			26 => VanillaItems::SPLASH_POTION()->setType(PotionType::SWIFTNESS),
			32 => VanillaItems::TOTEM(),
			33 => VanillaItems::TOTEM(),
			34 => VanillaItems::TOTEM(),
			35 => VanillaItems::SPLASH_POTION()->setType(PotionType::LONG_SWIFTNESS)
		];

		parent::__construct($armor, $items, []);
	}

	public function applyTo(CollapsePlayer $player) : void{
		$player->getOffHandInventory()->setItem(0, VanillaItems::TOTEM());
		parent::applyTo($player);
	}
}
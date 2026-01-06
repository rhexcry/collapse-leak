<?php

declare(strict_types=1);

namespace collapse\game\kit\modes;

use collapse\game\duel\item\DuelItems;
use collapse\game\kit\KitCollection;
use collapse\item\default\CollapseFishingRod;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\VanillaItems;

final class BuildUHC extends KitCollection{

	public function __construct(){
		parent::__construct(
			[
				VanillaItems::DIAMOND_HELMET()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 2))->setUnbreakable(),
				VanillaItems::DIAMOND_CHESTPLATE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 2))->setUnbreakable(),
				VanillaItems::DIAMOND_LEGGINGS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 2))->setUnbreakable(),
				VanillaItems::DIAMOND_BOOTS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION(), 2))->setUnbreakable()
			],
			[
				$this->markAsMainWeapon(VanillaItems::DIAMOND_SWORD()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS(), 2))->setUnbreakable()),
				(new CollapseFishingRod())->setUnbreakable(),
				VanillaItems::BOW()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::POWER(), 2))->setUnbreakable(),
				VanillaItems::LAVA_BUCKET(),
				VanillaItems::WATER_BUCKET(),
				VanillaItems::GOLDEN_APPLE()->setCount(6),
				DuelItems::GOLDEN_HEAD()->setCount(3),
				VanillaBlocks::COBBLESTONE()->asItem()->setCount(64),
				VanillaBlocks::OAK_PLANKS()->asItem()->setCount(64),
				17 => VanillaItems::ARROW()->setCount(32),
				28 => VanillaItems::DIAMOND_PICKAXE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 2))->setUnbreakable(),
				29 => VanillaItems::DIAMOND_AXE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::EFFICIENCY(), 2))->setUnbreakable(),
				30 => VanillaItems::LAVA_BUCKET(),
				31 => VanillaItems::WATER_BUCKET(),
				34 => VanillaBlocks::COBBLESTONE()->asItem()->setCount(64),
				35 => VanillaBlocks::OAK_PLANKS()->asItem()->setCount(64)
			],
			[]
		);
	}
}

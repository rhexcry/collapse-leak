<?php

declare(strict_types=1);

namespace collapse\game\kit\modes;

use collapse\game\kit\KitCollection;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\VanillaItems;

final class Build extends KitCollection{

	public const int SANDSTONE_COUNT = 64;

	public function __construct(){
		parent::__construct(
			[
				VanillaItems::GOLDEN_HELMET()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION()))->setUnbreakable(),
				VanillaItems::CHAINMAIL_CHESTPLATE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION()))->setUnbreakable(),
				VanillaItems::CHAINMAIL_LEGGINGS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION()))->setUnbreakable(),
				VanillaItems::GOLDEN_BOOTS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION()))->setUnbreakable()
			],
			[
				$this->markAsMainWeapon(VanillaItems::GOLDEN_SWORD()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::SHARPNESS()))->setUnbreakable()),
				VanillaBlocks::SMOOTH_SANDSTONE()->asItem()->setCount(self::SANDSTONE_COUNT),
				VanillaItems::GOLDEN_PICKAXE()->setUnbreakable(),
				VanillaItems::STICK()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::KNOCKBACK())),
				VanillaBlocks::COBWEB()->asItem()->setCount(5),
			],
			[]
		);
	}
}
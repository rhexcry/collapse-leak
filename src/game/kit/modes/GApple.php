<?php

declare(strict_types=1);

namespace collapse\game\kit\modes;

use collapse\game\kit\KitCollection;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\VanillaItems;

final class GApple extends KitCollection{

	public function __construct(){
		parent::__construct(
			[
				VanillaItems::DIAMOND_HELMET()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION()))->setUnbreakable(),
				VanillaItems::DIAMOND_CHESTPLATE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION()))->setUnbreakable(),
				VanillaItems::DIAMOND_LEGGINGS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION()))->setUnbreakable(),
				VanillaItems::DIAMOND_BOOTS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION()))->setUnbreakable()
			],
			[
				$this->markAsMainWeapon(VanillaItems::DIAMOND_SWORD()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING()))->setUnbreakable()),
				VanillaItems::GOLDEN_APPLE()->setCount(8)
			],
			[]
		);
	}
}

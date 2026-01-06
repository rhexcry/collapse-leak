<?php

declare(strict_types=1);

namespace collapse\game\kit\modes;

use collapse\game\kit\KitCollection;
use pocketmine\item\VanillaItems;

final class MidFight extends KitCollection{

	public function __construct(){
		parent::__construct(
			[
				VanillaItems::DIAMOND_HELMET()->setUnbreakable(),
				VanillaItems::DIAMOND_CHESTPLATE()->setUnbreakable(),
				VanillaItems::DIAMOND_LEGGINGS()->setUnbreakable(),
				VanillaItems::DIAMOND_BOOTS()->setUnbreakable(),
			],
			[
				$this->markAsMainWeapon(VanillaItems::STONE_SWORD()->setUnbreakable()),
			],
			[]
		);
	}
}

<?php

declare(strict_types=1);

namespace collapse\game\kit\modes;

use collapse\game\duel\item\Fireball;
use collapse\game\duel\item\TNT;
use collapse\game\kit\KitCollection;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;

final class FireballFight extends KitCollection{

	public function __construct(){
		parent::__construct(
			[
				$this->markAsTeamColor(VanillaItems::LEATHER_CAP()->setUnbreakable()),
				$this->markAsTeamColor(VanillaItems::LEATHER_TUNIC()->setUnbreakable()),
				$this->markAsTeamColor(VanillaItems::LEATHER_PANTS()->setUnbreakable()),
				$this->markAsTeamColor(VanillaItems::LEATHER_BOOTS()->setUnbreakable())
			],
			[
				$this->markAsMainWeapon(VanillaItems::STONE_SWORD()->setUnbreakable()),
				$this->markAsTeamColor(VanillaBlocks::WOOL()->asItem()->setCount(64)),
				VanillaBlocks::END_STONE()->asItem()->setCount(8),
				(new Fireball())->setCount(6),
				(new TNT())->setCount(3),
				VanillaItems::STONE_PICKAXE()->setUnbreakable(),
				VanillaItems::STONE_AXE()->setUnbreakable(),
				VanillaItems::SHEARS()->setUnbreakable(),
				VanillaBlocks::LADDER()->asItem()->setCount(8)
			],
			[]
		);
	}
}

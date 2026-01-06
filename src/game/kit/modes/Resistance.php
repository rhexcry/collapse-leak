<?php

declare(strict_types=1);

namespace collapse\game\kit\modes;

use collapse\game\kit\KitCollection;
use pocketmine\item\VanillaItems;

final class Resistance extends KitCollection{

	public function __construct(){
		parent::__construct(
			[],
			[
				$this->markAsMainWeapon(VanillaItems::DIAMOND_SWORD()->setUnbreakable()),
				VanillaItems::STICK()
			],
			[]
		);
	}
}

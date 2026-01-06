<?php

declare(strict_types=1);

namespace collapse\item\default;

use collapse\entity\CollapseEnderPearl as CollapseEnderPearlEntity;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\Throwable;
use pocketmine\item\EnderPearl;
use pocketmine\player\Player;

final class CollapseEnderPearl extends EnderPearl{
	use CollapseProjectileItemTrait;

	protected function createEntity(Location $location, Player $thrower) : Throwable{
		return new CollapseEnderPearlEntity($location, $thrower);
	}

	public function getThrowForce() : float{
		return 2.35;
	}
}

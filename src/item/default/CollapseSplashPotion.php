<?php

declare(strict_types=1);

namespace collapse\item\default;

use collapse\entity\CollapseSplashPotion as CollapseSplashPotionEntity;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\Throwable;
use pocketmine\item\SplashPotion;
use pocketmine\player\Player;

final class CollapseSplashPotion extends SplashPotion{
	use CollapseProjectileItemTrait;

	protected function createEntity(Location $location, Player $thrower) : Throwable{
		return new CollapseSplashPotionEntity($location, $thrower, $this->getType());
	}

	public function getThrowForce() : float{
		return 0.45;
	}
}

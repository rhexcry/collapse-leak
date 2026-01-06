<?php

declare(strict_types=1);

namespace collapse\item\default;

use collapse\player\CollapsePlayer;
use pocketmine\entity\Location;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\sound\ThrowSound;
use function array_filter;

trait CollapseProjectileItemTrait{

	public function onClickAir(Player $player, Vector3 $directionVector, array &$returnedItems) : ItemUseResult{
		$location = $player->getLocation();

		$projectile = $this->createEntity(Location::fromObject($player->getEyePos(), $player->getWorld(), $location->yaw, $location->pitch), $player);
		$projectile->setMotion($directionVector->multiply($this->getThrowForce()));

		$projectileEv = new ProjectileLaunchEvent($projectile);
		$projectileEv->call();
		if($projectileEv->isCancelled()){
			$projectile->flagForDespawn();
			return ItemUseResult::FAIL;
		}

		foreach(array_filter($player->getViewers(), fn(CollapsePlayer $target) : bool => $target->canSee($player)) as $target){
			$projectile->spawnTo($target);
		}

		$projectile->spawnTo($player);

		$location->getWorld()->addSound($location, new ThrowSound());

		$this->pop();

		$player->getInventory()->setItemInHand($this);

		return ItemUseResult::SUCCESS;
	}
}

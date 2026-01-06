<?php

declare(strict_types=1);

namespace collapse\cosmetics\potion;

use collapse\entity\CollapseSplashPotion;
use collapse\player\CollapsePlayer;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\Listener;

final readonly class PotionColorListener implements Listener{

	/**
	 * @priority MONITOR
	 */
	public function handleProjectileLaunch(ProjectileLaunchEvent $event) : void{
		$projectile = $event->getEntity();
		if($projectile instanceof CollapseSplashPotion){
			$owningEntity = $projectile->getOwningEntity();
			if($owningEntity instanceof CollapsePlayer && $owningEntity->getProfile()->getPotionColor() !== null){
				$projectile->setCustomColors($owningEntity->getProfile()->getPotionColor()->toColors());
			}
		}
	}
}
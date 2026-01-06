<?php

declare(strict_types=1);

namespace collapse\system\anticheat\check\combat\reach;

use collapse\player\CollapsePlayer;
use collapse\system\anticheat\AnticheatConstants;
use collapse\system\anticheat\AnticheatSession;
use collapse\system\anticheat\check\Check;
use collapse\utils\MathUtils;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Event;

final class ReachD extends Check{

	public function getName() : string{
		return 'Reach';
	}

	public function getSubType() : string{
		return 'D';
	}

	public function checkJustEvent(Event $event) : void{
		if($event instanceof EntityDamageByEntityEvent){
			$entity = $event->getEntity();
			$damager = $event->getDamager();
			if($damager instanceof CollapsePlayer && $entity instanceof CollapsePlayer){
				$entitySession = AnticheatSession::from($entity);
				$damagerSession = AnticheatSession::from($damager);

				if(
					$entitySession->getProjectileAttackTicks() < 40 ||
					$damagerSession->getProjectileAttackTicks() < 40 ||
					$entitySession->getBowShotTicks() < 40 ||
					$damagerSession->getBowShotTicks() < 40
				){
					return;
				}


				$player = $entitySession->getPlayer();
				$damager = $damagerSession->getPlayer();
				$playerLoc = $player->getLocation();
				$damagerLoc = $player->getLocation();
				$distance = MathUtils::XZDistanceSquared($playerLoc->asVector3(), $damagerLoc->asVector3());
				if($distance > ($damager->isSurvival() ? AnticheatConstants::SURVIVAL_MAX_DISTANCE : AnticheatConstants::CREATIVE_MAX_DISTANCE)){
					$this->debug($damagerSession, 'distance=' . $distance);
					$this->failed($damagerSession);
				}
			}
		}
	}

	public function getMaxViolations() : int{
		return 3;
	}

	public function getMaxFinalViolations() : int{
		return 1;
	}
}
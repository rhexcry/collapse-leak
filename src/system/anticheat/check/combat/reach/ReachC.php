<?php

declare(strict_types=1);

namespace collapse\system\anticheat\check\combat\reach;

use collapse\player\CollapsePlayer;
use collapse\system\anticheat\AnticheatConstants;
use collapse\system\anticheat\AnticheatSession;
use collapse\system\anticheat\check\Check;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Event;
use pocketmine\math\Vector3;

final class ReachC extends Check{

	public function getName() : string{
		return 'Reach';
	}

	public function getSubType() : string{
		return 'C';
	}

	public function checkJustEvent(Event $event) : void{
		if($event instanceof EntityDamageByEntityEvent){
			$victim = $event->getEntity();
			$damager = $event->getDamager();
			if($victim instanceof CollapsePlayer && $damager instanceof CollapsePlayer){
				$victimSession = AnticheatSession::from($victim);
				$damagerSession = AnticheatSession::from($damager);

				if(
					$victimSession->getProjectileAttackTicks() < 40 ||
					$damagerSession->getProjectileAttackTicks() < 40 ||
					$victimSession->getBowShotTicks() < 40 ||
					$damagerSession->getBowShotTicks() < 40
				){
					return;
				}

				$eyeHeight = $damager->getEyePos();
				$cuboid = $victim->getBoundingBox();
				$distance = $eyeHeight->distance(new Vector3($cuboid->minX, $cuboid->minY, $cuboid->minZ));
				if($distance > AnticheatConstants::MAX_REACH_EYE_DISTANCE){
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
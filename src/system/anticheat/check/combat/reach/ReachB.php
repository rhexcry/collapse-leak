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
use pocketmine\player\GameMode;

final class ReachB extends Check{

	public function getName() : string{
		return 'Reach';
	}

	public function getSubType() : string{
		return 'B';
	}

	public function checkJustEvent(Event $event) : void{
		if($event instanceof EntityDamageByEntityEvent){
			$entity = $event->getEntity();
			$damager = $event->getDamager();

			if($damager instanceof CollapsePlayer && $entity instanceof CollapsePlayer){
				$damagerSession = AnticheatSession::from($damager);
				$playerSession = AnticheatSession::from($entity);

				if(
					$playerSession->getProjectileAttackTicks() < 40 ||
					$damagerSession->getProjectileAttackTicks() < 40 ||
					$playerSession->getBowShotTicks() < 40 ||
					$damagerSession->getBowShotTicks() < 40
				){
					return;
				}

				$locEntity = $entity->getLocation();
				$locDamager = $damager->getLocation();
				$isPlayerTop = $locEntity->getY() > $locDamager->getY() ? abs($locEntity->getY() - $locDamager->getY()) : 0;
				$distance = MathUtils::distance($locEntity, $locDamager) - $isPlayerTop;
				$isSurvival = $entity->getGameMode() === GameMode::SURVIVAL();
				if($isSurvival && $distance > AnticheatConstants::SURVIVAL_MAX_DISTANCE){
					$this->debug($damagerSession, 'isPlayerTop=' . $isPlayerTop . ', distance=' . $distance . ', isSurvival=' . $isSurvival);
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
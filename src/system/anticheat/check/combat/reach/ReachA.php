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

final class ReachA extends Check{

	public function getName() : string{
		return 'Reach';
	}

	public function getSubType() : string{
		return 'A';
	}

	public function checkJustEvent(Event $event) : void{
		if($event instanceof EntityDamageByEntityEvent){
			$damager = $event->getDamager();
			$player = $event->getEntity();

			if($player instanceof CollapsePlayer && $damager instanceof CollapsePlayer){
				$damagerSession = AnticheatSession::from($damager);
				$playerSession = AnticheatSession::from($player);

				if(
					$playerSession->getProjectileAttackTicks() < 40 ||
					$damagerSession->getProjectileAttackTicks() < 40 ||
					$playerSession->getBowShotTicks() < 40 ||
					$damagerSession->getBowShotTicks() < 40
				){
					return;
				}

				$damagerPing = $damager->getNetworkSession()->getPing();
				$playerPing = $player->getNetworkSession()->getPing();
				$distance = $player->getEyePos()->distance(new Vector3($damager->getEyePos()->getX(), $player->getEyePos()->getY(), $damager->getEyePos()->getZ()));
				$distance -= $damagerPing * AnticheatConstants::DEFAULT_EYE_DISTANCE;
				$distance -= $playerPing * AnticheatConstants::DEFAULT_EYE_DISTANCE;
				$limit = AnticheatConstants::REACH_EYE_LIMIT;

				if($player->isSprinting()){
					$distance -= AnticheatConstants::SPRINTING_EYE_DISTANCE;
				}else{
					$distance -= AnticheatConstants::NOT_SPRINTING_EYE_DISTANCE;
				}

				if($damager->isSprinting()){
					$distance -= AnticheatConstants::DAMAGER_SPRINTING_EYE_DISTANCE;
				}else{
					$distance -= AnticheatConstants::DAMAGER_NOT_SPRINTING_EYE_DISTANCE;
				}

				if($distance > $limit){
					$this->debug($damagerSession, 'distance=' . $distance . ', limit=' . $limit);
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
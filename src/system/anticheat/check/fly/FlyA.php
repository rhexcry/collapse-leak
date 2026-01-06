<?php

declare(strict_types=1);

namespace collapse\system\anticheat\check\fly;

use collapse\system\anticheat\AnticheatConstants;
use collapse\system\anticheat\AnticheatSession;
use collapse\system\anticheat\check\Check;
use collapse\utils\BlockUtils;
use pocketmine\network\mcpe\protocol\DataPacket;

final class FlyA extends Check{

	public function getName() : string{
		return 'Fly';
	}

	public function getSubType() : string{
		return 'A';
	}

	public function check(DataPacket $packet, AnticheatSession $session) : void{
		$player = $session->getPlayer();
		if(
			$session->getAttackTicks() < 40 ||
			$session->getOnlineTime() <= 30 ||
			$session->getJumpTicks() < 40 ||
			$session->isInWeb() ||
			$session->isOnGround() ||
			$session->isOnAdhesion() ||
			$player->getAllowFlight() ||
			$player->hasNoClientPredictions() ||
			!$player->isSurvival() ||
			!$session->isCurrentChunkIsLoaded() ||
			BlockUtils::isGroundSolid($player) ||
			$session->isGliding()
		){
			$session->unsetExternalData("lastYNoGroundF");
			$session->unsetExternalData("lastTimeF");
			return;
		}
		$lastYNoGround = $session->getExternalData("lastYNoGroundF");
		$lastTime = $session->getExternalData("lastTimeF");
		if($lastYNoGround !== null && $lastTime !== null){
			$diff = microtime(true) - $lastTime;
			if($diff > AnticheatConstants::MAX_GROUND_DIFF){
				if((int) $player->getLocation()->getY() == $lastYNoGround){
					$this->debug($session, 'diff=' . $diff . ', lastTime=' . $lastTime . ', lastYNoGround=' . $lastYNoGround);
					$this->failed($session);
				}

				$session->unsetExternalData("lastYNoGroundF");
				$session->unsetExternalData("lastTimeF");
			}
		}else{
			$session->setExternalData("lastYNoGroundF", (int) $player->getLocation()->getY());
			$session->setExternalData("lastTimeF", microtime(true));
		}
	}

	public function getMaxViolations() : int{
		return 1;
	}

	public function getMaxFinalViolations() : int{
		return 1;
	}
}
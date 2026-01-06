<?php

declare(strict_types=1);

namespace collapse\system\observe;

use collapse\player\CollapsePlayer;
use collapse\Practice;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;

final class ObserveListener implements Listener{

	public function handlePlayerQuit(PlayerQuitEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		$observeManager = Practice::getInstance()->getObserveManager();
		foreach($observeManager->getSessionsByTarget($player) as $session){
			if($session->getObserver() !== null and $session->getObserver()->isConnected()){
				$observeManager->stopObserving($session->getObserver());
			}
		}
	}
}
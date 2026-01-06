<?php

declare(strict_types=1);

namespace collapse\game\duel\queue;

use collapse\game\duel\queue\event\DuelMatchFoundEvent;
use collapse\player\CollapsePlayer;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;

final readonly class QueueListener implements Listener{

	public function __construct(
		private QueueManager $queueManager
	){}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerQuit(PlayerQuitEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		$this->queueManager->removeFromSoloQueue($player);
	}

	/**
	 * @priority LOWEST
	 */
	public function handleEntityTeleport(EntityTeleportEvent $event) : void{
		$player = $event->getEntity();
		if($player instanceof CollapsePlayer && $this->queueManager->isInQueue($player) && $event->getTo()->getWorld() !== $event->getFrom()->getWorld()){
			$this->queueManager->removeFromSoloQueue($player);
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handleMatchFound(DuelMatchFoundEvent $event) : void{
		$queue = $event->getQueue();
		if(!$queue instanceof UnrankedQueue){
			return;
		}

		[$first, $second] = $event->getMembers();

		if($first->getProfile()->isProfileBannedInQueue($second->getProfile()) || $second->getProfile()->isProfileBannedInQueue($first->getProfile())){
			$event->cancel();
		}
	}
}

<?php

declare(strict_types=1);

namespace collapse\system\internal\packetlimiter;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;

final readonly class PacketLimiterListener implements Listener{
	public function __construct(
		private PacketLimiterManager $manager
	){}

	public function handleDataPacketReceive(DataPacketReceiveEvent $event) : void{
		$player = $event->getOrigin()->getPlayer();
		if($player === null){
			return;
		}
		$this->manager->updateList($player->getName());
	}

	public function handlePlayerQuit(PlayerQuitEvent $event) : void{
		$this->manager->removeFromList($event->getPlayer()->getName());
	}
}
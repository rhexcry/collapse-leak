<?php

declare(strict_types=1);

namespace collapse\player\profile;

use collapse\player\CollapsePlayer;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\RequestChunkRadiusPacket;

final readonly class ProfileListener implements Listener{

	public function __construct(
		private ProfileManager $profileManager
	){}

	/**
	 * @priority LOWEST
	 */
	public function handleDataPacketReceive(DataPacketReceiveEvent $event) : void{
		$packet = $event->getPacket();
		if($packet instanceof RequestChunkRadiusPacket){
			$player = $event->getOrigin()->getPlayer();
			if($player instanceof CollapsePlayer){
				if($player->getProfile() === null && !$player->isWaitingProfileLoad()){
					$event->cancel();
					$this->profileManager->onPlayerConnect($player, $packet);
				}
			}
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerQuit(PlayerQuitEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		$this->profileManager->onPlayerDisconnect($player);
	}

	public function handlePlayerPreLogin(PlayerPreLoginEvent $event) : void{
		$this->profileManager->onPlayerPreLogin($event);
	}
}

<?php

declare(strict_types=1);

namespace collapse\cosmetics\capes;

use collapse\player\CollapsePlayer;
use collapse\player\profile\event\ProfileLoadedEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChangeSkinEvent;

final readonly class CapesListener implements Listener{

	public function __construct(
		private CapesManager $capesManager
	){}

	/**
	 * @priority LOWEST
	 */
	public function handleProfileLoaded(ProfileLoadedEvent $event) : void{
		$profile = $event->getProfile();
		if($profile->getCape() === null){
			return;
		}
		$this->capesManager->setCapeOnSkin($profile, $profile->getCape());
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerChangeSkin(PlayerChangeSkinEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		if($player->getProfile()?->getCape() === null){
			return;
		}
		$event->setNewSkin($this->capesManager->getSkinWithCape($event->getNewSkin(), $player->getProfile()->getCape()));
	}
}

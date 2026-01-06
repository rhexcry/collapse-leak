<?php

declare(strict_types=1);

namespace collapse\punishments;

use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\player\profile\event\ProfileLoadedEvent;
use collapse\punishments\rule\PunishmentRules;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;

final readonly class PunishmentListener implements Listener{

	public function __construct(
		private PunishmentManager $punishmentManager
	){}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerChat(PlayerChatEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();

		$punishment = $player->getMutePunishment();
		if($punishment !== null){
			$player->sendTranslatedMessage(CollapseTranslationFactory::chat_muted(
				PunishmentRules::getRule($punishment->getReason())?->getTranslation(true) ?? $punishment->getReason(),
				$this->punishmentManager->convertExpires($punishment)
			));
			$event->cancel();
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handleProfileLoaded(ProfileLoadedEvent $event) : void{
		$player = $event->getProfile()->getPlayer();
		$player?->setMutePunishment($this->punishmentManager->getMutePunishmentByXuid($player->getXuid()));
	}
}

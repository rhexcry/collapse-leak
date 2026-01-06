<?php

declare(strict_types=1);

namespace collapse\i18n;

use collapse\i18n\event\ProfileChangeLanguageEvent;
use collapse\i18n\types\Language;
use collapse\item\CollapseItem;
use collapse\player\profile\event\ProfileLoadedEvent;
use pocketmine\event\Listener;

final readonly class TranslatorListener implements Listener{

	public function __construct(
		private TranslatorManager $translatorManager
	){}

	/**
	 * @priority LOWEST
	 */
	public function handleProfileLoaded(ProfileLoadedEvent $event) : void{
		$profile = $event->getProfile();
		if($profile->getLanguage() === null){
			$translator = $this->translatorManager->fromLocale($profile->getPlayer()->getPlayerInfo()->getLocale());
			$profile->setLanguage($translator->getCurrentLanguage());
		}
		$language = Language::fromString($profile->getLanguage());
		$profile->setTranslator($this->translatorManager->fromLocale($language->getCode()));
	}

	/**
	 * @priority LOWEST
	 */
	public function handleProfileChangedLanguage(ProfileChangeLanguageEvent $event) : void{
		$player = $event->getProfile()->getPlayer();
		if($player === null){
			return;
		}
		$player->getNetworkSession()->syncAvailableCommands();
		$inventory = $player->getInventory();
		foreach($inventory->getContents() as $index => $item){
			if(!$item instanceof CollapseItem){
				continue;
			}
			$inventory->setItem($index, $item->translate($player));
		}
		$scoreboard = $player->getScoreboard();
		$scoreboard?->setUp();
		$scoreboard?->flushUpdates();
	}
}

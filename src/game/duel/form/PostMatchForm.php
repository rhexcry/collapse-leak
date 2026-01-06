<?php

declare(strict_types=1);

namespace collapse\game\duel\form;

use collapse\form\SimpleForm;
use collapse\game\duel\inventory\PostMatchInventory;
use collapse\game\duel\records\DuelRecord;
use collapse\game\duel\types\DuelType;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use function array_merge;

final class PostMatchForm extends SimpleForm{

	private const int BUTTON_QUEUE_AGAIN = 0;

	public function __construct(CollapsePlayer $player, DuelRecord $record){
		parent::__construct(function(CollapsePlayer $player, ?int $data = null) use ($record) : void{
			if($data === null){
				return;
			}
			switch($data){
				case self::BUTTON_QUEUE_AGAIN:
					Practice::getInstance()->getDuelManager()->getQueueManager()->joinSoloQueue(
						$player,
						$record->getMode(),
						$record->getType() === DuelType::Ranked
					);
					break;
				default:
					$index = $data - 1;
					$players = array_merge($record->getWinners(), $record->getLosers());
					if(isset($players[$index])){
						$inventory = new PostMatchInventory($record, $players[$index], 10);
						$inventory->open($player);
					}else{
						$player->sendForm(new PostMatchForm($player, $record));
					}
					break;
			}
		});
		$translator = $player->getProfile()->getTranslator();
		$this->setTitle($translator->translate(CollapseTranslationFactory::duels_post_match_form_title()));
		$this->addButton($translator->translate(CollapseTranslationFactory::duels_post_match_form_button_queue_again()));
		$profileManager = Practice::getInstance()->getProfileManager();
		foreach(array_merge($record->getWinners(), $record->getLosers()) as $xuid){
			$profile = Practice::getPlayerByXuid($xuid)?->getProfile() ?? $profileManager->getProfileByXuid($xuid);
			$this->addButton($translator->translate(CollapseTranslationFactory::duels_post_match_form_button_player(
				$profile->getRank()->toColor() . $profile->getPlayerName()
			)));
		}
	}
}

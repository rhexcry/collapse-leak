<?php

declare(strict_types=1);

namespace collapse\game\duel\form;

use collapse\form\SimpleForm;
use collapse\game\duel\types\DuelType;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\resourcepack\CollapseUI;
use collapse\resourcepack\Font;

final class DuelsForm extends SimpleForm{

	private const int BUTTON_UNRANKED_DUELS = 0;
	private const int BUTTON_RANKED_DUELS = 1;
	private const int BUTTON_INCOMING_INVITES = 2;
	private const int BUTTON_SPECTATE_DUELS = 3;

	public function __construct(CollapsePlayer $player){
		$duelManager = Practice::getInstance()->getDuelManager();
		$requestManager = $duelManager->getRequestManager();
		parent::__construct(static function(CollapsePlayer $player, ?int $data = null) use ($requestManager) : void{
			if($data === null){
				return;
			}
			switch($data){
				case self::BUTTON_UNRANKED_DUELS:
					$player->sendForm(new UnrankedDuelsForm($player));
					break;
				case self::BUTTON_RANKED_DUELS:
					$player->sendForm(new RankedDuelsForm($player));
					break;
				case self::BUTTON_INCOMING_INVITES:
					if(empty($requestManager->getRequests($player))){
						$player->sendTranslatedMessage(CollapseTranslationFactory::duels_form_no_incoming_invites());
						break;
					}
					$player->sendForm(new IncomingInvitesForm($player));
					break;
				case self::BUTTON_SPECTATE_DUELS:
					$player->sendForm(new SpectateDuelsForm());
					break;
			}
		});
		$queueManager = $duelManager->getQueueManager();
		$translator = $player->getProfile()->getTranslator();
		$this->setTitle(Font::bold($translator->translate(CollapseTranslationFactory::duels_form_title())));
		$this->addButton(CollapseTranslationFactory::duels_form_button_unranked(
			(string) $queueManager->getSoloUnrankedInQueue(),
			(string) $duelManager->getPlaying(DuelType::Unranked)
		), SimpleForm::IMAGE_TYPE_PATH, CollapseUI::UNRANKED_FORM_LOGO);
		$this->addButton(CollapseTranslationFactory::duels_form_button_ranked(
			(string) $queueManager->getSoloRankedInQueue(),
			(string) $duelManager->getPlaying(DuelType::Ranked)
		), SimpleForm::IMAGE_TYPE_PATH, CollapseUI::RANKED_FORM_LOGO);
		$this->addButton(
			CollapseTranslationFactory::duels_form_button_incoming_invites(),
			SimpleForm::IMAGE_TYPE_PATH, CollapseUI::INCOMING_INVITES_FORM_LOGO
		);
		$this->addButton(
			CollapseTranslationFactory::duels_form_button_spectate_duels(),
			SimpleForm::IMAGE_TYPE_PATH, CollapseUI::SPECTATE_FORM_LOGO
		);
	}
}

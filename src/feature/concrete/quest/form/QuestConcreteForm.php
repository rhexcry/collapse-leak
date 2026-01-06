<?php

declare(strict_types=1);

namespace collapse\feature\concrete\quest\form;

use collapse\feature\concrete\quest\Quest;
use collapse\feature\concrete\quest\QuestFeature;
use collapse\form\SimpleForm;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\resourcepack\Font;

final class QuestConcreteForm extends SimpleForm{

	public function __construct(private readonly CollapsePlayer $player, private readonly Quest $quest){
		parent::__construct(function(CollapsePlayer $player, ?int $data = null) : void{
			if($data === null){
				return;
			}

			match($data){
				0 => $player->sendForm(new AvailableQuestsForm($player))
			};

		});

		$translator = $this->player->getProfile()->getTranslator();

		$this->setTitle(Font::bold($translator->translate($this->quest->getName())));
		$progress = Practice::getInstance()->getFeatureManager()->get(QuestFeature::class)->getQuestManager()->getPlayerProgress($this->player->getProfile(), $this->quest->getId());
		$progressMessage = '';
		foreach($progress->toDisplay() as $dis){
			$progressMessage .= $translator->translate($dis) . "\n";
		}
		$this->setContent(
			$translator->translate($this->quest->getDescription()) .
			"\n\n" . $progressMessage);

		$this->addButton(CollapseTranslationFactory::form_button_go_back());
	}
}

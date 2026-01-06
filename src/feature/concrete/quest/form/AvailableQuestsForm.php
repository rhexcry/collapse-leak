<?php

declare(strict_types=1);

namespace collapse\feature\concrete\quest\form;

use collapse\feature\concrete\quest\Quest;
use collapse\feature\concrete\quest\QuestFeature;
use collapse\form\SimpleForm;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\resourcepack\CollapseUI;
use collapse\resourcepack\Font;
use Symfony\Component\Filesystem\Path;
use function array_values;

final class AvailableQuestsForm extends SimpleForm{

	public function __construct(private readonly CollapsePlayer $player){
		$quests = Practice::getInstance()->getFeatureManager()->get(QuestFeature::class)->getQuestManager()->getAllQuests();
		parent::__construct(function(CollapsePlayer $player, ?int $data = null) use ($quests) : void{
			if($data === null){
				return;
			}

			$quest = array_values($quests)[$data];

			$player->sendForm(new QuestConcreteForm($player, $quest));
		});

		$translator = $this->player->getProfile()->getTranslator();

		$this->setTitle(CollapseUI::HEADER_FORM_GRID . Font::bold($translator->translate(CollapseTranslationFactory::quest_form_available_quests_title())));

		foreach($quests as $quest){
			/** @var Quest $quest */
			$this->addButton($quest->getName(), SimpleForm::IMAGE_TYPE_PATH, Path::join(CollapseUI::QUESTS_ICONS, $quest->getIconPath()));
		}
	}
}

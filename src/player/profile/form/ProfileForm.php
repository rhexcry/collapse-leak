<?php

declare(strict_types=1);

namespace collapse\player\profile\form;

use collapse\form\SimpleForm;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\player\settings\SettingsForm;
use collapse\resourcepack\CollapseUI;
use collapse\resourcepack\Font;
use collapse\system\friend\form\FriendsForm;
use collapse\system\kiteditor\form\KitSelectionForm;

final class ProfileForm extends SimpleForm{

	private const int BUTTON_SETTINGS = 0;
	private const int BUTTON_DUELS_UNRANKED_STATISTICS = 1;
	private const int BUTTON_DUELS_RANKED_STATISTICS = 2;
	private const int BUTTON_FREE_FOR_ALL_STATISTICS = 3;
	private const int BUTTON_FRIENDS = 4;
	private const int BUTTON_KIT_EDITOR = 5;

	public function __construct(CollapsePlayer $player){
		parent::__construct(static function(CollapsePlayer $player, ?int $data = null) : void{
			if($data === null){
				return;
			}
			switch($data){
				case self::BUTTON_SETTINGS:
					$player->sendForm(new SettingsForm($player));
					break;
				case self::BUTTON_DUELS_UNRANKED_STATISTICS:
					$player->sendForm(new UnrankedDuelsStatisticsForm($player));
					break;
				case self::BUTTON_DUELS_RANKED_STATISTICS:
					$player->sendForm(new RankedDuelsStatisticsForm($player));
					break;
				case self::BUTTON_FREE_FOR_ALL_STATISTICS:
					$player->sendForm(new FreeForAllStatisticsForm($player));
					break;
				case self::BUTTON_FRIENDS:
					$player->sendForm(new FriendsForm($player));
					break;
				case self::BUTTON_KIT_EDITOR:
					$player->sendForm(new KitSelectionForm($player));
					break;
			}
		});
		$translator = $player->getProfile()->getTranslator();
		$this->setTitle(Font::bold($translator->translate(CollapseTranslationFactory::profile_form_title())));
		$this->addButton(
			Font::text($translator->translate(CollapseTranslationFactory::profile_form_button_settings())),
			SimpleForm::IMAGE_TYPE_PATH, CollapseUI::SETTINGS_FORM_LOGO
		);
		$this->addButton(
			Font::text($translator->translate(CollapseTranslationFactory::profile_form_button_unranked_duels_statistics())),
			self::IMAGE_TYPE_PATH, CollapseUI::PROFILE_UNRANKED_STATISTICS_FORM_LOGO
		);
		$this->addButton(
			Font::text($translator->translate(CollapseTranslationFactory::profile_form_button_ranked_duels_statistics())),
			self::IMAGE_TYPE_PATH, CollapseUI::PROFILE_RANKED_STATISTICS_FORM_LOGO
		);
		$this->addButton(
			Font::text($translator->translate(CollapseTranslationFactory::profile_form_button_free_for_all_statistics())),
			self::IMAGE_TYPE_PATH, CollapseUI::PROFILE_FFA_STATISTICS_FORM_LOGO
		);
		$this->addButton(
			Font::text($translator->translate(CollapseTranslationFactory::profile_form_button_friends())),
			SimpleForm::IMAGE_TYPE_PATH, CollapseUI::FRIENDS_FORM_LOGO
		);
		$this->addButton(
			Font::text($translator->translate(CollapseTranslationFactory::profile_form_button_kit_editor())),
			SimpleForm::IMAGE_TYPE_PATH, CollapseUI::KIT_EDITOR_LOGO
		);
	}
}

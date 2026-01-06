<?php

declare(strict_types=1);

namespace collapse\cosmetics;

use collapse\cosmetics\capes\CapeSelectionForm;
use collapse\cosmetics\effects\death\DeathEffectSelectionForm;
use collapse\cosmetics\potion\PotionColorSelectionForm;
use collapse\cosmetics\tags\ChatTagSelectionForm;
use collapse\form\SimpleForm;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\player\rank\Rank;
use collapse\resourcepack\CollapseUI;
use collapse\resourcepack\Font;

final class CosmeticsForm extends SimpleForm{

	private const int BUTTON_TAGS = 0;
	private const int BUTTON_CAPES = 1;
	private const int BUTTON_DEATH_EFFECTS = 2;
	private const int BUTTON_POTION_COLORS = 3;

	public function __construct(CollapsePlayer $player){
		parent::__construct(static function(CollapsePlayer $player, ?int $data = null) : void{
			if($data === null){
				return;
			}

			switch($data){
				case self::BUTTON_TAGS:
					$player->sendForm(new ChatTagSelectionForm($player));
					break;
				case self::BUTTON_CAPES:
					$player->sendForm(new CapeSelectionForm($player));
					break;
				case self::BUTTON_DEATH_EFFECTS:
					$player->sendForm(new DeathEffectSelectionForm($player));
					break;
				case self::BUTTON_POTION_COLORS:
					if($player->getProfile()->getRank()->getPriority() < Rank::LUMINOUS->getPriority()){
						$player->sendTranslatedMessage(CollapseTranslationFactory::potion_color_unavailable(Rank::LUMINOUS->toFont()));
						break;
					}
					$player->sendForm(new PotionColorSelectionForm($player));
					break;
			}
		});
		$translator = $player->getProfile()->getTranslator();
		$this->setTitle(Font::bold($translator->translate(CollapseTranslationFactory::cosmetics_form_title())));
		$this->addButton(
			Font::bold($translator->translate(CollapseTranslationFactory::cosmetics_form_button_tags())),
			self::IMAGE_TYPE_PATH, CollapseUI::COSMETICS_CHAT_TAGS_FORM_LOGO
		);
		$this->addButton(
			Font::bold($translator->translate(CollapseTranslationFactory::cosmetics_form_button_capes())),
			self::IMAGE_TYPE_PATH, CollapseUI::COSMETICS_CAPE_FORM_LOGO
		);
		$this->addButton(
			Font::bold($translator->translate(CollapseTranslationFactory::cosmetics_form_button_death_effects())),
			self::IMAGE_TYPE_PATH, CollapseUI::COSMETICS_DEATH_EFFECT_FORM_LOGO
		);
		$this->addButton(
			Font::bold($translator->translate(CollapseTranslationFactory::cosmetics_form_button_potion_colors())),
			self::IMAGE_TYPE_PATH, CollapseUI::COSMETICS_POTION_COLORS_FORM_LOGO
		);
	}
}

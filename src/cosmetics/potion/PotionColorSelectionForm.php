<?php

declare(strict_types=1);

namespace collapse\cosmetics\potion;

use collapse\form\CustomForm;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\resourcepack\Font;
use function array_column;
use function array_map;
use function array_merge;
use function array_search;
use function array_values;

final class PotionColorSelectionForm extends CustomForm{

	public function __construct(CollapsePlayer $player){
		$potionColors = PotionColor::cases();
		parent::__construct(static function(CollapsePlayer $player, ?array $data = null) use ($potionColors) : void{
			if($data === null){
				return;
			}
			$potionColorManager = Practice::getInstance()->getCosmeticsManager()->getPotionColorManager();
			if($data[0] === 0){
				$potionColorManager->onChangePotionColor($player->getProfile(), null);
				return;
			}
			$selected = array_values($potionColors)[$data[0] - 1] ?? null;
			if(!$selected instanceof PotionColor){
				return;
			}
			if(!$selected->canUse($player->getProfile())){
				$player->sendTranslatedMessage(CollapseTranslationFactory::cosmetics_cant_equip());
				return;
			}
			$potionColorManager->onChangePotionColor($player->getProfile(), $selected);
		});
		$translator = $player->getProfile()->getTranslator();
		$this->setTitle(Font::bold($translator->translate(CollapseTranslationFactory::potion_color_selection_form_title())));
		$selected = array_search(
			$player->getProfile()->getPotionColor()?->value ?? '',
			array_column($potionColors, 'value'),
			true
		);
		$this->addDropdown(
			Font::bold($translator->translate(CollapseTranslationFactory::potion_color_selection_form_dropdown())),
			array_merge(
				[Font::bold($translator->translate(CollapseTranslationFactory::potion_color_selection_form_none()))],
				array_map(static fn(PotionColor $potionColor) : string => Font::bold($translator->translate($potionColor->toDisplayName())), $potionColors)
			),
			$selected === false ? 0 : $selected + 1
		);
	}
}
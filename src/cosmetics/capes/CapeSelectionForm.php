<?php

declare(strict_types=1);

namespace collapse\cosmetics\capes;

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

final class CapeSelectionForm extends CustomForm{

	public function __construct(CollapsePlayer $player){
		$capes = Cape::getAvailableCapes($player);
		parent::__construct(static function(CollapsePlayer $player, ?array $data = null) use ($capes) : void{
			if($data === null){
				return;
			}
			$capesManager = Practice::getInstance()->getCosmeticsManager()->getCapesManager();
			if($data[0] === 0){
				$capesManager->onChangeCape($player->getProfile(), null);
				return;
			}
			$selected = array_values($capes)[$data[0] - 1] ?? null;
			if(!$selected instanceof Cape){
				return;
			}
			if(!$selected->canUse($player->getProfile())){
				$player->sendTranslatedMessage(CollapseTranslationFactory::cosmetics_cant_equip());
				return;
			}
			$capesManager->onChangeCape($player->getProfile(), $selected);
		});
		$translator = $player->getProfile()->getTranslator();
		$this->setTitle(Font::bold($translator->translate(CollapseTranslationFactory::capes_selection_form_title())));
		$selected = array_search(
			$player->getProfile()->getCape()?->value ?? '',
			array_column($capes, 'value'),
			true
		);
		$this->addDropdown(
			Font::bold($translator->translate(CollapseTranslationFactory::capes_selection_form_dropdown())),
			array_merge(
				[Font::bold($translator->translate(CollapseTranslationFactory::capes_selection_form_none()))],
				array_map(static fn(Cape $cape) : string => Font::bold($cape->toDisplayName()), $capes)
			),
			$selected === false ? 0 : $selected + 1
		);
	}
}

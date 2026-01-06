<?php

declare(strict_types=1);

namespace collapse\cosmetics\effects\death;

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

class DeathEffectSelectionForm extends CustomForm{

	public function __construct(CollapsePlayer $player){
		$deathEffects = DeathEffectType::getAvailableDeathEffects($player);
		parent::__construct(static function(CollapsePlayer $player, ?array $data = null) use ($deathEffects) : void{
			if($data === null){
				return;
			}
			$effectsManager = Practice::getInstance()->getCosmeticsManager()->getEffectsManager();
			if($data[0] === 0){
				$effectsManager->setDeathEffect($player->getProfile(), null);
				return;
			}
			$selected = array_values($deathEffects)[$data[0] - 1] ?? null;
			if(!$selected instanceof DeathEffectType){
				return;
			}
			if(!$selected->canUse($player->getProfile())){
				$player->sendTranslatedMessage(CollapseTranslationFactory::cosmetics_cant_equip());
				return;
			}
			$effectsManager->setDeathEffect($player->getProfile(), $selected);
		});
		$translator = $player->getProfile()->getTranslator();
		$this->setTitle(Font::bold($translator->translate(CollapseTranslationFactory::death_effect_selection_form_title())));
		$selected = array_search(
			$player->getProfile()->getDeathEffect()?->value ?? '',
			array_column($deathEffects, 'value'),
			true
		);
		$this->addDropdown(
			Font::bold($translator->translate(CollapseTranslationFactory::death_effect_selection_form_dropdown())),
			array_merge(
				[Font::bold($translator->translate(CollapseTranslationFactory::death_effect_form_none()))],
				array_map(static fn(DeathEffectType $deathEffect) : string => Font::bold($deathEffect->toDisplayName()), $deathEffects)
			),
			$selected === false ? 0 : $selected + 1
		);
	}
}

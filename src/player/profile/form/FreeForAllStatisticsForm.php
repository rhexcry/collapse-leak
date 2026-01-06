<?php

declare(strict_types=1);

namespace collapse\player\profile\form;

use collapse\form\SimpleForm;
use collapse\game\ffa\types\FreeForAllMode;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\resourcepack\CollapseUI;
use collapse\resourcepack\Font;
use Symfony\Component\Filesystem\Path;
use function number_format;

final class FreeForAllStatisticsForm extends SimpleForm{

	public function __construct(CollapsePlayer $player){
		$modes = FreeForAllMode::cases();
		$profile = $player->getProfile();
		$translator = $profile->getTranslator();
		parent::__construct(static function(CollapsePlayer $player, ?int $data = null) use ($modes, $profile, $translator) : void{
			if($data === null){
				return;
			}
			$mode = $modes[$data] ?? null;
			if($mode === null){
				return;
			}
			$player->sendForm((new SimpleForm(static function(CollapsePlayer $player, ?int $data = null) : void{
				if($data === null){
					return;
				}
				$player->sendForm(new FreeForAllStatisticsForm($player));
			}))
				->setTitle(Font::bold($translator->translate(CollapseTranslationFactory::free_for_all_statistics_form_title())))
				->setContent(
					CollapseTranslationFactory::free_for_all_statistics_form_button(
						Font::bold($mode->toDisplayName()),
						number_format($profile->getFreeForAllKills($mode)),
						number_format($profile->getFreeForAllDeaths($mode)),
						number_format($profile->getFreeForAllKillDeathRatio($mode), 2),
						number_format($profile->getFreeForAllKillStreak($mode)),
						number_format($profile->getFreeForAllBestKillStreak($mode))
					)
				)
				->addButton(
					Font::bold($translator->translate(CollapseTranslationFactory::form_button_go_back()))
				)
			);
		});
		$this->setTitle(CollapseUI::HEADER_FORM_GRID . Font::bold($translator->translate(CollapseTranslationFactory::free_for_all_statistics_form_title())));
		foreach($modes as $mode){
			$this->addButton(
				CollapseTranslationFactory::free_for_all_statistics_form_button(
					Font::bold($mode->toDisplayName()),
					number_format($profile->getFreeForAllKills($mode)),
					number_format($profile->getFreeForAllDeaths($mode)),
					number_format($profile->getFreeForAllKillDeathRatio($mode), 2),
					number_format($profile->getFreeForAllKillStreak($mode)),
					number_format($profile->getFreeForAllBestKillStreak($mode))
				),
				SimpleForm::IMAGE_TYPE_PATH,
				Path::join(CollapseUI::GAME_MODE_ICONS, $mode->toTexture())
			);
		}
	}
}

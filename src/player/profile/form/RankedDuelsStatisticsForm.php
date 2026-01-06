<?php

declare(strict_types=1);

namespace collapse\player\profile\form;

use collapse\form\SimpleForm;
use collapse\game\duel\types\DuelMode;
use collapse\game\duel\types\DuelType;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\resourcepack\CollapseUI;
use collapse\resourcepack\Font;
use Symfony\Component\Filesystem\Path;
use function number_format;

final class RankedDuelsStatisticsForm extends SimpleForm{

	public function __construct(CollapsePlayer $player){
		$modes = DuelMode::ranked();
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
				$player->sendForm(new RankedDuelsStatisticsForm($player));
			}))
				->setTitle(Font::bold($translator->translate(CollapseTranslationFactory::duels_statistics_form_title(DuelType::Ranked->name))))
				->setContent(
					CollapseTranslationFactory::duels_ranked_statistics_form_button(
						Font::bold($mode->toDisplayName()),
						number_format($profile->getDuelsElo($mode)),
						number_format($profile->getDuelsPeekElo($mode)),
						number_format($profile->getDuelsWins(DuelType::Ranked, $mode)),
						number_format($profile->getDuelsLosses(DuelType::Ranked, $mode)),
						number_format($profile->getDuelsPlays(DuelType::Ranked, $mode)),
						number_format($profile->getDuelsWinrate(DuelType::Ranked, $mode), 2) . Font::CHAR_PERCENT,
						number_format($profile->getDuelsWinStreak(DuelType::Ranked, $mode)),
						number_format($profile->getDuelsBestWinStreak(DuelType::Ranked, $mode))
					)
				)
				->addButton(
					Font::bold($translator->translate(CollapseTranslationFactory::form_button_go_back()))
				)
			);
		});
		$this->setTitle(CollapseUI::HEADER_FORM_GRID . Font::bold($translator->translate(CollapseTranslationFactory::duels_statistics_form_title(DuelType::Ranked->name))));
		foreach($modes as $mode){
			$this->addButton(
				CollapseTranslationFactory::duels_ranked_statistics_form_button(
					Font::bold($mode->toDisplayName()),
					number_format($profile->getDuelsElo($mode)),
					number_format($profile->getDuelsPeekElo($mode)),
					number_format($profile->getDuelsWins(DuelType::Ranked, $mode)),
					number_format($profile->getDuelsLosses(DuelType::Ranked, $mode)),
					number_format($profile->getDuelsPlays(DuelType::Ranked, $mode)),
					number_format($profile->getDuelsWinrate(DuelType::Ranked, $mode), 2) . Font::CHAR_PERCENT,
					number_format($profile->getDuelsWinStreak(DuelType::Ranked, $mode)),
					number_format($profile->getDuelsBestWinStreak(DuelType::Ranked, $mode))
				),
				SimpleForm::IMAGE_TYPE_PATH,
				Path::join(CollapseUI::GAME_MODE_ICONS, $mode->toTexture())
			);
		}
	}
}

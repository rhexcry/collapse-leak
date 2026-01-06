<?php

declare(strict_types=1);

namespace collapse\leaderboard\form;

use collapse\form\SimpleForm;
use collapse\game\duel\types\DuelMode;
use collapse\i18n\CollapseTranslationFactory;
use collapse\leaderboard\LeaderboardType;
use collapse\leaderboard\LeaderboardUtils;
use collapse\leaderboard\ProfileLeaderboardEntry;
use collapse\leaderboard\types\DuelsRankedElo;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\resourcepack\CollapseUI;
use collapse\resourcepack\Font;
use Symfony\Component\Filesystem\Path;
use function array_map;
use function implode;
use const EOL;

final class DuelsRankedEloForm extends SimpleForm{

	private const int BUTTON_GLOBAL = 0;

	public function __construct(CollapsePlayer $player){
		parent::__construct(static function(CollapsePlayer $player, ?int $data = null) : void{
			if($data === null){
				return;
			}

			$translator = $player->getProfile()->getTranslator();
			$leaderboard = Practice::getInstance()->getLeaderboardManager()->getLeaderboard(LeaderboardType::DuelsRankedElo);
			if(!$leaderboard instanceof DuelsRankedElo){
				return;
			}

			if($data === self::BUTTON_GLOBAL){
				$player->sendForm((new SimpleForm(static function(CollapsePlayer $player, ?int $data = null) : void{
					if($data === null){
						return;
					}
					$player->sendForm(new DuelsRankedEloForm($player));
				}))->setTitle(Font::bold($translator->translate(CollapseTranslationFactory::leaderboard_ranked_duels_form_title())))
					->setContent(
						Font::bold($translator->translate(CollapseTranslationFactory::leaderboard_ranked_duels_global_form_button())) . EOL . EOL .
						implode(EOL, array_map(static function(ProfileLeaderboardEntry $entry) : string{
							return LeaderboardUtils::simpleFormatProfileLeaderboardEntry($entry);
						}, $leaderboard->getGlobalEntries() ?? []))
					)
					->addButton(Font::bold($translator->translate(CollapseTranslationFactory::form_button_go_back()))));
				return;
			}
			$mode = DuelMode::ranked()[$data - 1] ?? null;
			if($mode === null){
				return;
			}
			$player->sendForm((new SimpleForm(static function(CollapsePlayer $player, ?int $data = null) : void{
				if($data === null){
					return;
				}
				$player->sendForm(new DuelsRankedEloForm($player));
			}))->setTitle(Font::bold($translator->translate(CollapseTranslationFactory::leaderboard_ranked_duels_form_title())))
				->setContent(
					Font::bold($translator->translate(CollapseTranslationFactory::leaderboard_ranked_duels_local_form_button(Font::whiteBold($mode->toDisplayName())))) . EOL . EOL .
					implode(EOL, array_map(static function(ProfileLeaderboardEntry $entry) : string{
						return LeaderboardUtils::simpleFormatProfileLeaderboardEntry($entry);
					}, $leaderboard->getEntries($mode) ?? []))
				)
				->addButton(Font::bold($translator->translate(CollapseTranslationFactory::form_button_go_back()))));
		});

		$translator = $player->getProfile()->getTranslator();
		$this->setTitle(CollapseUI::HEADER_FORM_GRID . Font::bold($translator->translate(CollapseTranslationFactory::leaderboard_ranked_duels_form_title())));
		$leaderboard = Practice::getInstance()->getLeaderboardManager()->getLeaderboard(LeaderboardType::DuelsRankedElo);
		if(!$leaderboard instanceof DuelsRankedElo){
			return;
		}

		$this->addButton(
			Font::bold($translator->translate(CollapseTranslationFactory::leaderboard_ranked_duels_global_form_button())) . EOL . EOL .
			implode(EOL, array_map(static function(ProfileLeaderboardEntry $entry) : string{
				return LeaderboardUtils::simpleFormatProfileLeaderboardEntry($entry);
			}, $leaderboard->getGlobalEntries() ?? [])),
			SimpleForm::IMAGE_TYPE_PATH,
			CollapseUI::RANKED_FORM_LOGO
		);

		foreach(DuelMode::ranked() as $mode){
			$this->addButton(
				Font::bold($translator->translate(CollapseTranslationFactory::leaderboard_ranked_duels_local_form_button(Font::whiteBold($mode->toDisplayName())))) . EOL . EOL .
				implode(EOL, array_map(static function(ProfileLeaderboardEntry $entry) : string{
					return LeaderboardUtils::simpleFormatProfileLeaderboardEntry($entry);
				}, $leaderboard->getEntries($mode) ?? [])),
				SimpleForm::IMAGE_TYPE_PATH,
				Path::join(CollapseUI::GAME_MODE_ICONS, $mode->toTexture())
			);
		}
	}
}

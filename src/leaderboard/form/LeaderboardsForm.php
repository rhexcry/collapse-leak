<?php

declare(strict_types=1);

namespace collapse\leaderboard\form;

use collapse\form\SimpleForm;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\resourcepack\Font;

final class LeaderboardsForm extends SimpleForm{

	private const int BUTTON_RANKED_DUELS_ELO = 0;
	private const int BUTTON_UNRANKED_DUELS_WINS = 1;
	private const int BUTTON_UNRANKED_DUELS_BEST_WIN_STREAK = 2;
	private const int BUTTON_FREE_FOR_ALL_KILLS = 3;

	public function __construct(CollapsePlayer $player){
		parent::__construct(static function(CollapsePlayer $player, ?int $data = null) : void{
			if($data === null){
				return;
			}

			switch($data){
				case self::BUTTON_RANKED_DUELS_ELO:
					$player->sendForm(new DuelsRankedEloForm($player));
					break;
				case self::BUTTON_UNRANKED_DUELS_WINS:
					$player->sendForm(new DuelsUnrankedWinsForm($player));
					break;
				case self::BUTTON_UNRANKED_DUELS_BEST_WIN_STREAK:
					$player->sendForm(new DuelsUnrankedWinStreakForm($player));
					break;
				case self::BUTTON_FREE_FOR_ALL_KILLS:
					$player->sendForm(new FreeForAllKillsForm($player));
					break;
			}
		});
		$translator = $player->getProfile()->getTranslator();
		$this->setTitle(Font::bold($translator->translate(CollapseTranslationFactory::leaderboards_form_title())));
		$this->addButton(Font::bold($translator->translate(CollapseTranslationFactory::leaderboards_form_button_ranked_elo())));
		$this->addButton(Font::bold($translator->translate(CollapseTranslationFactory::leaderboards_form_button_unranked_duels_wins())));
		$this->addButton(Font::bold($translator->translate(CollapseTranslationFactory::leaderboards_form_button_unranked_duels_best_win_streak())));
		$this->addButton(Font::bold($translator->translate(CollapseTranslationFactory::leaderboards_form_button_free_for_all_kills())));
	}
}

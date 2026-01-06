<?php

declare(strict_types=1);

namespace collapse\lobby;

use collapse\i18n\CollapseTranslationFactory;
use collapse\player\scoreboard\CollapseScoreboard;
use collapse\Practice;
use collapse\PracticeConstants;
use collapse\resourcepack\Font;
use collapse\wallet\currency\Currencies;
use collapse\wallet\Wallet;
use pocketmine\utils\TextFormat;
use function count;
use function number_format;

final class LobbyScoreboard extends CollapseScoreboard{

	public function setUp() : void{
		$playing = Practice::getInstance()->getDuelManager()->getPlaying() + Practice::getInstance()->getFreeForAllManager()->getPlaying();
		$profile = $this->player->getProfile();
		$this->setLines([
			1 => TextFormat::BLACK . Font::SCOREBOARD_LINE,
			2 => $profile->getTranslator()->translate(CollapseTranslationFactory::lobby_scoreboard_profile()),
			3 => $profile->getTranslator()->translate(CollapseTranslationFactory::lobby_scoreboard_kd(
				number_format($profile->getFreeForAllKills()),
				number_format($profile->getFreeForAllDeaths())
			)),
			4 => $profile->getTranslator()->translate(CollapseTranslationFactory::lobby_scoreboard_kdr(number_format($profile->getFreeForAllKillDeathRatio(), 2))),
			5 => $profile->getTranslator()->translate(CollapseTranslationFactory::lobby_scoreboard_dust(number_format(Wallet::get(Currencies::DUST(), $profile)))),
			6 => null,
			7 => $profile->getTranslator()->translate(CollapseTranslationFactory::lobby_scoreboard_games()),
			8 => $profile->getTranslator()->translate(CollapseTranslationFactory::lobby_scoreboard_online((string) count(Practice::onlinePlayers()))),
			9 => $profile->getTranslator()->translate(CollapseTranslationFactory::lobby_scoreboard_playing((string) $playing)),
			10 => null,
			11 => ' ' . Font::bold(PracticeConstants::STORE_LINK),
			12 => TextFormat::GRAY . Font::SCOREBOARD_LINE
		]);
	}

	public function onUpdate() : void{
		$playing = Practice::getInstance()->getDuelManager()->getPlaying() + Practice::getInstance()->getFreeForAllManager()->getPlaying();
		$profile = $this->player->getProfile();
		$this->setLine(5, $profile->getTranslator()->translate(CollapseTranslationFactory::lobby_scoreboard_dust(number_format(Wallet::get(Currencies::DUST(), $profile)))));
		$this->setLine(8, $profile->getTranslator()->translate(CollapseTranslationFactory::lobby_scoreboard_online((string) count(Practice::onlinePlayers()))));
		$this->setLine(9, $profile->getTranslator()->translate(CollapseTranslationFactory::lobby_scoreboard_playing((string) $playing)));
	}
}

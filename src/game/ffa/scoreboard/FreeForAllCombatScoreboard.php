<?php

declare(strict_types=1);

namespace collapse\game\ffa\scoreboard;

use collapse\game\ffa\FreeForAllArena;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\scoreboard\CollapseScoreboard;
use collapse\player\scoreboard\HiddenScoreboard;
use collapse\PracticeConstants;
use collapse\resourcepack\Font;
use pocketmine\utils\TextFormat;

final class FreeForAllCombatScoreboard extends CollapseScoreboard implements HiddenScoreboard{

	public function setUp() : void{
		$arena = $this->player->getGame();
		if(!$arena instanceof FreeForAllArena){
			return;
		}
		$opponent = $arena->getOpponentManager()->getOpponent($this->player);
		if($opponent === null){
			return;
		}
		$this->setLines([
			1 => TextFormat::BLACK . Font::SCOREBOARD_LINE,
			2 => CollapseTranslationFactory::free_for_all_combat_scoreboard_opponent(),
			//reserved
			4 => null,
			//reserved
			//reserved
			7 => null,
			8 => ' ' . Font::bold(PracticeConstants::STORE_LINK),
			9 => TextFormat::GRAY . Font::SCOREBOARD_LINE,
		]);
		$this->onUpdate();
	}

	public function onUpdate() : void{
		$arena = $this->player->getGame();
		if(!$arena instanceof FreeForAllArena){
			return;
		}
		$opponent = $arena->getOpponentManager()->getOpponent($this->player);
		if($opponent === null || !$opponent->isConnected()){
			return;
		}
		$this->setLine(3, CollapseTranslationFactory::free_for_all_combat_scoreboard_opponent_info(
			$opponent->getNameWithRankColor(),
			(string) $opponent->getNetworkSession()->getPing()
		));
		$this->setLine(5, CollapseTranslationFactory::free_for_all_combat_scoreboard_combat_time(
			(string) $arena->getOpponentManager()->getCombatTime($this->player)
		));
		$this->setLine(6, CollapseTranslationFactory::free_for_all_combat_scoreboard_your_ping((string) $this->player->getNetworkSession()->getPing()));
	}
}

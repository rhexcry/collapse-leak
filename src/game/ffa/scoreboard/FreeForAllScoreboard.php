<?php

declare(strict_types = 1);

namespace collapse\game\ffa\scoreboard;

use collapse\game\ffa\FreeForAllArena;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\scoreboard\CollapseScoreboard;
use collapse\PracticeConstants;
use collapse\resourcepack\Font;
use pocketmine\utils\TextFormat;
use function count;

final class FreeForAllScoreboard extends CollapseScoreboard{

	public function setUp() : void{
		$arena = $this->player->getGame();
		if(!$arena instanceof FreeForAllArena){
			return;
		}
		$translator = $this->player->getProfile()->getTranslator();
		$this->setLines([
			1 => TextFormat::BLACK . Font::SCOREBOARD_LINE,
			2 => $translator->translate(CollapseTranslationFactory::free_for_all_scoreboard_mode(Font::minecraftColorToUnicodeFont($arena->getConfig()->getMode()->toDisplayName()))),
			//reserved
			4 => null,
			//reserved
			6 => null,
			7 => ' ' . Font::bold(PracticeConstants::STORE_LINK),
			8 => TextFormat::GRAY . Font::SCOREBOARD_LINE,
		]);
		$this->onUpdate();
	}

	public function onUpdate() : void{
		$arena = $this->player->getGame();
		if(!$arena instanceof FreeForAllArena){
			return;
		}
		$this->setLine(3, CollapseTranslationFactory::free_for_all_scoreboard_playing((string) count($arena->getPlayerManager()->getPlayers())));
		$this->setLine(5, CollapseTranslationFactory::free_for_all_scoreboard_your_ping((string) $this->player->getNetworkSession()->getPing()));
	}
}

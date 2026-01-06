<?php

declare(strict_types=1);

namespace collapse\game\duel\phase\end;

use collapse\game\duel\phase\PhaseScoreboard;
use collapse\i18n\CollapseTranslationFactory;
use collapse\PracticeConstants;
use collapse\resourcepack\Font;
use pocketmine\utils\TextFormat;
use function date;

class PhaseEndScoreboard extends PhaseScoreboard{

	public function setUp() : void{
		$victory = $this->duel->getRecord()->hasWinner($this->player->getXuid());
		$duration = $this->duel->getRecord()->getDuration();
		$this->setLines([
			1 => TextFormat::GRAY . Font::SCOREBOARD_LINE,
			2 => $victory ? CollapseTranslationFactory::duels_phase_end_scoreboard_victory() : CollapseTranslationFactory::duels_phase_end_scoreboard_defeat(),
			3 => null,
			4 => CollapseTranslationFactory::duels_phase_end_scoreboard_duration(date($duration > 3600 ? 'H:i:s' : 'i:s', $duration)),
			5 => null,
			6 => ' ' . Font::bold(PracticeConstants::STORE_LINK),
			7 => TextFormat::BLACK . Font::SCOREBOARD_LINE
		]);
	}

	public function onUpdate() : void{}
}

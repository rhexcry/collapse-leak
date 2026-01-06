<?php

declare(strict_types=1);

namespace collapse\game\duel\modes\basic;

use collapse\game\duel\phase\PhaseScoreboard;
use collapse\i18n\CollapseTranslationFactory;
use collapse\PracticeConstants;
use collapse\resourcepack\Font;
use pocketmine\utils\TextFormat;
use function assert;
use function count;

abstract class BedsScoreboard extends PhaseScoreboard{

	private int $pingLine;

	public function setUp() : void{
		assert($this->duel instanceof BedsDuel);
		$line = 1;
		$lines = [];
		$lines[$line++] = Font::SCOREBOARD_LINE;
		foreach($this->duel->getTeamManager()->getTeams() as $team){
			$lines[$line++] = $this->duel->getBedManager()->isBedAlive($team) ? CollapseTranslationFactory::duels_base_bed_scoreboard_alive(
				$team->getColor(),
				$team->getName(),
				$this->player->getTeam() === $team ? CollapseTranslationFactory::duels_base_scoreboard_your_team() : ''
			) : CollapseTranslationFactory::duels_base_bed_scoreboard_destroyed(
				$team->getColor(),
				$team->getName(),
				(string) count($team->getPlayers()),
				$this->player->getTeam() === $team ? CollapseTranslationFactory::duels_base_scoreboard_your_team() : ''
			);
		}
		$lines[$line++] = null;
		$lines[$this->pingLine = $line++] = CollapseTranslationFactory::duels_phase_running_scoreboard_your_ping((string) $this->player->getNetworkSession()->getPing());
		$lines[$line++] = null;
		$lines[$line++] = ' ' . Font::bold(PracticeConstants::STORE_LINK);
		$lines[$line] = TextFormat::BLACK . Font::SCOREBOARD_LINE;
		$this->setLines($lines);
	}

	public function onForceUpdate() : void{
		assert($this->duel instanceof BedsDuel);
		$line = 2;
		foreach($this->duel->getTeamManager()->getTeams() as $team){
			$this->setLine($line++, $this->duel->getBedManager()->isBedAlive($team) ? CollapseTranslationFactory::duels_base_bed_scoreboard_alive(
				$team->getColor(),
				$team->getName(),
				$this->player->getTeam() === $team ? CollapseTranslationFactory::duels_base_scoreboard_your_team() : ''
			) : CollapseTranslationFactory::duels_base_bed_scoreboard_destroyed(
				$team->getColor(),
				$team->getName(),
				(string) count($team->getPlayers()),
				$this->player->getTeam() === $team ? CollapseTranslationFactory::duels_base_scoreboard_your_team() : ''
			));
		}
	}

	public function onUpdate() : void{
		$this->setLine($this->pingLine, CollapseTranslationFactory::duels_phase_running_scoreboard_your_ping((string) $this->player->getNetworkSession()->getPing()));
	}
}

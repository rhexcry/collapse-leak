<?php

declare(strict_types=1);

namespace collapse\game\duel\phase\countdown;

use collapse\game\duel\phase\Phase;
use collapse\game\duel\phase\PhaseScoreboard;
use collapse\game\duel\types\DuelType;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\PracticeConstants;
use collapse\resourcepack\Font;
use pocketmine\utils\TextFormat;
use function array_values;

final class PhaseCountdownScoreboard extends PhaseScoreboard{

	/** @var PhaseCountdown */
	protected readonly Phase $phase;

	public function setUp() : void{
		if($this->duel->getType() === DuelType::PartyRequest){
			$this->setTeamScoreboard();
		}else{
			$this->setSoloScoreboard();
		}

		$this->onUpdate();
	}

	private function setTeamScoreboard() : void{
		$lines = [
			1 => TextFormat::GRAY . Font::SCOREBOARD_LINE,
			2 => null,
			3 => null,
			4 => null,
			5 => null,
			6 => null,
			7 => null,
			8 => null,
			9 => ' ' . Font::bold(PracticeConstants::STORE_LINK),
			10 => TextFormat::BLACK . Font::SCOREBOARD_LINE
		];

		$this->setLines($lines);
	}

	private function setSoloScoreboard() : void{
		$this->setLines([
			1 => TextFormat::GRAY . Font::SCOREBOARD_LINE,
			2 => CollapseTranslationFactory::duels_phase_countdown_scoreboard_opponent(),
			4 => null,
			5 => CollapseTranslationFactory::duels_phase_countdown_scoreboard_your_ping((string) $this->player->getNetworkSession()->getPing()),
			6 => null,
			7 => ' ' . Font::bold(PracticeConstants::STORE_LINK),
			8 => TextFormat::BLACK . Font::SCOREBOARD_LINE
		]);
	}

	public function onUpdate() : void{
		if($this->duel->getType() === DuelType::PartyRequest){
			$this->updateTeamScoreboard();
		}else{
			$this->updateSoloScoreboard();
		}
	}

	private function updateTeamScoreboard() : void{
		$playerTeam = $this->player->getTeam();
		$opponentTeam = $this->duel->getOpponentManager()->getOpponent($playerTeam);

		if($playerTeam === null || $opponentTeam === null){
			return;
		}

		$playerTeamMembers = array_values($playerTeam->getPlayers());
		$opponentTeamMembers = array_values($opponentTeam->getPlayers());

		$translator = $this->player->getProfile()->getTranslator();

		$this->setLine(2, CollapseTranslationFactory::duels_phase_countdown_scoreboard_team($playerTeam->getColor() . $translator->translate($playerTeam->getName())));
		$this->setLine(5, CollapseTranslationFactory::duels_phase_countdown_scoreboard_team($opponentTeam->getColor() . $translator->translate($opponentTeam->getName())));

		$lineIndex = 3;
		foreach($playerTeamMembers as $index => $teamPlayer){
			if($teamPlayer instanceof CollapsePlayer){
				$playerInfo = null;
				if(!$this->duel->getPlayerManager()->isLoser($teamPlayer)){
					$playerInfo = CollapseTranslationFactory::duels_phase_countdown_scoreboard_team_player_info_alive(Font::minecraftColorToUnicodeFont($teamPlayer->getNameWithRankColor()), (string) $teamPlayer->getNetworkSession()->getPing());
				}else{
					$playerInfo = CollapseTranslationFactory::duels_phase_countdown_scoreboard_team_player_info_died(Font::minecraftColorToUnicodeFont($teamPlayer->getNameWithRankColor()));
				}

				$this->setLine($lineIndex, $playerInfo);
				$lineIndex++;
			}
		}

		$lineIndex = 6;
		foreach($opponentTeamMembers as $index => $opponentPlayer){
			if($opponentPlayer instanceof CollapsePlayer){
				$playerInfo = null;
				if(!$this->duel->getPlayerManager()->isLoser($opponentPlayer)){
					$playerInfo = CollapseTranslationFactory::duels_phase_countdown_scoreboard_team_player_info_alive(Font::minecraftColorToUnicodeFont($opponentPlayer->getNameWithRankColor()), (string) $opponentPlayer->getNetworkSession()->getPing());
				}else{
					$playerInfo = CollapseTranslationFactory::duels_phase_countdown_scoreboard_team_player_info_died(Font::minecraftColorToUnicodeFont($opponentPlayer->getNameWithRankColor()));
				}

				$this->setLine($lineIndex, $playerInfo);
				$lineIndex++;
			}
		}
	}

	private function updateSoloScoreboard() : void{
		$this->setLine(5, CollapseTranslationFactory::duels_phase_countdown_scoreboard_your_ping((string) $this->player->getNetworkSession()->getPing()));

		$opponent = $this->duel->getOpponentManager()->getOpponent($this->player);
		if($opponent === null){
			return;
		}

		$this->setLine(3, CollapseTranslationFactory::duels_phase_countdown_scoreboard_opponent_info(
			$opponent->getNameWithRankColor(),
			(string) $opponent->getNetworkSession()->getPing()
		));
	}
}
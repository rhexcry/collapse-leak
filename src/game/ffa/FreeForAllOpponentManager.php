<?php

declare(strict_types=1);

namespace collapse\game\ffa;

use collapse\game\ffa\modes\build\Build;
use collapse\game\ffa\modes\crystal\Crystal;
use collapse\game\ffa\modes\midfight\MidFight;
use collapse\game\ffa\scoreboard\FreeForAllCombatScoreboard;
use collapse\game\ffa\scoreboard\FreeForAllScoreboard;
use collapse\game\statistics\GameStatisticsManager;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\player\settings\Setting;
use collapse\Practice;
use function time;

final class FreeForAllOpponentManager{

	public const int COMBAT_TIME = 15;

	public function __construct(
		private readonly FreeForAllArena $arena
	){
	}

	/** @var array{playerName: string, data: array{opponent: CollapsePlayer, time: int, statistics: GameStatisticsManager}} */
	private array $opponents = [];

	public function getOpponent(CollapsePlayer $player) : ?CollapsePlayer{
		$opponent = $this->opponents[$player->getName()]['opponent'] ?? null;
		if($opponent !== null && !$opponent->isConnected()){
			return null;
		}
		return $this->opponents[$player->getName()]['opponent'] ?? null;
	}

	public function getCombatTime(CollapsePlayer $player) : ?int{
		if($this->getOpponent($player) === null){
			return null;
		}
		if(!isset($this->opponents[$player->getName()]['time'])){
			return null;
		}
		return $this->opponents[$player->getName()]['time'] - time();
	}

	public function getStatistics(CollapsePlayer $player) : ?GameStatisticsManager{
		if($this->getOpponent($player) === null){
			return null;
		}
		return $this->opponents[$player->getName()]['statistics'] ?? null;
	}

	public function updateCombatTime(CollapsePlayer $player) : void{
		if(($opponent = $this->getOpponent($player)) !== null){
			$this->opponents[$player->getName()]['time'] = time() + self::COMBAT_TIME;
			$this->opponents[$opponent->getName()]['time'] = time() + self::COMBAT_TIME;
		}
	}

	public function onPlayerSpawn(CollapsePlayer $spawnedPlayer) : void{
		foreach($this->opponents as $data){
			$player = $data['opponent'];
			$opponent = $this->getOpponent($player);

			if($opponent !== null && $opponent !== $spawnedPlayer && $player->getGame() !== null){
				if($player->getGame()->isHidePlayersInCombat()){
					$this->tryToHidePlayers($player, $opponent);
				}
			}
		}
	}

	private function tryToHidePlayers(CollapsePlayer $player, CollapsePlayer $opponent) : void{
		if($player->getProfile()->getSetting(Setting::HidePlayersInFreeForAll)){
			foreach(Practice::onlinePlayers() as $target){
				if($target === $opponent || !$player->canSee($target)){
					continue;
				}
				$player->hidePlayer($target);
			}
		}
	}

	private function tryToShowPlayers(CollapsePlayer $player) : void{
		foreach(Practice::onlinePlayers() as $target){
			if(!$player->canSee($target)){
				$player->showPlayer($target);
			}
		}
	}

	public function setInCombat(CollapsePlayer $player, CollapsePlayer $opponent) : void{
		$statisticsManager = $this->arena->isStatisticsEnabled() ? $this->arena->createStatistics([$player, $opponent]) : null;
		$this->opponents[$player->getName()] = ['opponent' => $opponent, 'time' => time() + self::COMBAT_TIME, 'statistics' => $statisticsManager];
		$this->opponents[$opponent->getName()] = ['opponent' => $player, 'time' => time() + self::COMBAT_TIME, 'statistics' => $statisticsManager];

		$game = $player->getGame();

		if(!$game instanceof Build && !$game instanceof Crystal && !$game instanceof MidFight){
			$player->sendTranslatedMessage(CollapseTranslationFactory::free_for_all_entered_combat($opponent->getNameWithRankColor()));
			$opponent->sendTranslatedMessage(CollapseTranslationFactory::free_for_all_entered_combat($player->getNameWithRankColor()));
		}

		$player->setScoreboard(new FreeForAllCombatScoreboard($player));
		$opponent->setScoreboard(new FreeForAllCombatScoreboard($opponent));

		$game = $player->getGame();

		if($game !== null && $game->isHidePlayersInCombat()){
			$this->tryToHidePlayers($player, $opponent);
			$this->tryToHidePlayers($opponent, $player);
		}
	}

	public function removeFromCombat(CollapsePlayer $player) : void{
		if(($opponent = $this->getOpponent($player)) === null){
			return;
		}

		if($player->getGame() !== null){
			$player->setScoreboard(new FreeForAllScoreboard($player));
		}
		if($opponent->isConnected() && $opponent->getGame() !== null){
			$opponent->setScoreboard(new FreeForAllScoreboard($opponent));
		}

		$game = $player->getGame();
		if($game !== null && $game->isHidePlayersInCombat()){
			$this->tryToShowPlayers($player);
			if($opponent->isConnected()){
				$this->tryToShowPlayers($opponent);
			}
		}

		unset($this->opponents[$player->getName()], $this->opponents[$opponent->getName()]);
	}

	public function update() : void{
		foreach($this->arena->getPlayerManager()->getPlayers() as $player){
			$combatTime = $this->getCombatTime($player);
			if($combatTime !== null && $combatTime <= 0){
				$this->removeFromCombat($player);
			}
		}
	}
}

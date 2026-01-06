<?php

declare(strict_types=1);

namespace collapse\game\duel;

use collapse\game\duel\phase\countdown\PhaseCountdown;
use collapse\game\duel\phase\DuelPhaseManager;
use collapse\game\duel\phase\end\PhaseEnd;
use collapse\game\duel\phase\running\PhaseRunning;
use collapse\game\duel\records\DuelRecord;
use collapse\game\duel\types\DuelType;
use collapse\game\Game;
use collapse\game\kb\KnockBack;
use collapse\game\kit\KitCollection;
use collapse\game\kit\Kits;
use collapse\game\statistics\GameStatisticsManager;
use collapse\game\teams\TeamManager;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use function time;

abstract class Duel implements Game{

	protected readonly KitCollection $kit;

	protected readonly KnockBack $knockBack;

	private readonly DuelPlayerManager $playerManager;

	private readonly DuelSpectatorManager $spectatorManager;

	private readonly DuelSpawnManager $spawnManager;

	protected readonly DuelPhaseManager $phaseManager;

	private readonly DuelOpponentManager $opponentManager;

	private readonly DuelBlockManager $blockManager;

	private readonly GameStatisticsManager $statisticsManager;

	private readonly DuelRecord $record;

	public function __construct(
		private readonly int $id,
		private readonly Practice $plugin,
		private readonly DuelConfig $config,
		private readonly DuelType $type,
		private readonly DuelWorldManager $worldManager,
		private readonly TeamManager $teamManager
	){
		$this->teamManager->setGame($this);
		$this->kit = Kits::get($this->config->getMode()->toKit());
		$this->knockBack = DuelKnockBacks::get($this->config->getMode());
		$this->playerManager = new DuelPlayerManager($this);
		$this->spectatorManager = new DuelSpectatorManager($this);
		$this->spawnManager = new DuelSpawnManager($this);
		$this->phaseManager = new DuelPhaseManager($this);
		$this->phaseManager->setPhase(new PhaseCountdown($this));
		$this->phaseManager->setBasePhaseRunning(new PhaseRunning($this));
		$this->opponentManager = new DuelOpponentManager($this);
		$this->blockManager = new DuelBlockManager();
		$this->statisticsManager = GameStatisticsManager::simple($this);
		$this->record = new DuelRecord(
			$this->config->getMode(),
			$this->type,
			[],
			[],
			time(),
			0,
			[],
			[],
			[]
		);

		$this->setUp();
	}

	protected function setUp() : void{}

	public function getId() : int{
		return $this->id;
	}

	public function getPlugin() : Practice{
		return $this->plugin;
	}

	public function getConfig() : DuelConfig{
		return $this->config;
	}

	public function getType() : DuelType{
		return $this->type;
	}

	public function getWorldManager() : DuelWorldManager{
		return $this->worldManager;
	}

	public function getTeamManager() : TeamManager{
		return $this->teamManager;
	}

	public function getKit() : KitCollection{
		return $this->kit;
	}

	public function getKnockBack() : KnockBack{
		return $this->knockBack;
	}

	public function getPlayerManager() : DuelPlayerManager{
		return $this->playerManager;
	}

	public function getSpectatorManager() : DuelSpectatorManager{
		return $this->spectatorManager;
	}

	public function getSpawnManager() : DuelSpawnManager{
		return $this->spawnManager;
	}

	public function getPhaseManager() : DuelPhaseManager{
		return $this->phaseManager;
	}

	public function getOpponentManager() : DuelOpponentManager{
		return $this->opponentManager;
	}

	public function getBlockManager() : DuelBlockManager{
		return $this->blockManager;
	}

	public function getStatisticsManager() : GameStatisticsManager{
		return $this->statisticsManager;
	}

	public function getStatistics(?CollapsePlayer $player = null) : ?GameStatisticsManager{
		return $this->statisticsManager;
	}

	public function getRecord() : DuelRecord{
		return $this->record;
	}

	public function onPlayerLeave(CollapsePlayer $player) : void{
		$isInEndPhase = $this->phaseManager->getPhase() instanceof PhaseEnd;
		if($this->playerManager->hasPlayer($player)){
			if(!$isInEndPhase){
				$this->playerManager->onPlayerDied($player);
			}else{
				$this->playerManager->removePlayer($player);
			}
		}

		if($this->playerManager->isLoser($player)){
			$this->playerManager->removeLoser($player);
		}
		if($this->type->isSolo()){
			$this->opponentManager->removeOpponent($player);
		}
		if($this->spectatorManager->hasSpectator($player)){
			$this->spectatorManager->removeSpectator($player);
		}
	}

	public function isBlocksActions() : bool{
		return false;
	}

	public function isDamageDisabled() : bool{
		return false;
	}

	public function isFallDamageDisabled() : bool{
		return true;
	}

	public function isEnderPearlCooldown() : bool{
		return false;
	}

	public function close() : void{
		$this->playerManager->close();
		$this->spectatorManager->close();
		$this->worldManager->close();
	}

	public function isCombat() : bool{
		return true;
	}

	public function isHidePlayersInCombat() : bool{
		return true;
	}

	public function isStatisticsEnabled() : bool{
		return true;
	}

	public function hasRandomSpawn() : bool{
		return false;
	}
}

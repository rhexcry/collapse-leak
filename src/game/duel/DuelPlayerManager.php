<?php

declare(strict_types=1);

namespace collapse\game\duel;

use collapse\game\duel\event\DuelVictoryEvent;
use collapse\game\duel\form\PostMatchForm;
use collapse\game\duel\phase\end\PhaseEnd;
use collapse\game\duel\records\DuelRecord;
use collapse\game\statistics\GameStatistics;
use collapse\game\teams\Team;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\world\sound\MinecraftSound;
use collapse\world\sound\MinecraftSoundNames;
use pocketmine\lang\Translatable;
use pocketmine\player\GameMode;
use function array_key_first;
use function array_keys;
use function array_merge;
use function count;

final class DuelPlayerManager{

	/** @var CollapsePlayer[] */
	private array $players = [];

	/** @var (CollapsePlayer|null)[] */
	private array $losers = [];

	public function __construct(
		private readonly Duel $duel
	){
	}

	public function getPlayers() : array{
		return $this->players;
	}

	public function hasPlayer(CollapsePlayer $player) : bool{
		return isset($this->players[$player->getName()]);
	}

	public function getLosers() : array{
		return $this->losers;
	}

	public function isLoser(CollapsePlayer $player) : bool{
		return isset($this->losers[$player->getXuid()]);
	}

	public function removeLoser(CollapsePlayer $player) : void{
		unset($this->losers[$player->getXuid()]);
	}

	public function setPlayerNameTag(CollapsePlayer $player) : void{
		if($this->duel->getConfig()->getMode()->isUsingTeamColor()){
			$player->setNameTag($player->getTeam()->getColor() . $player->getName());
		}else{
			$this->duel->getPlugin()->getRankManager()->setPlayerNameTag($player);
		}
	}

	public function addPlayer(CollapsePlayer $player) : void{
		$team = $this->duel->getTeamManager()->getFreeTeam();
		if($team === null){
			return;
		}

		if(!$player->isConnected()){
			return;
		}
		$this->players[$player->getName()] = $player;
		$player->setScoreboard(null);
		$player->setGame($this->duel);
		$team->addPlayer($player);
		$player->setTeam($team);
		$player->setBasicProperties($this->duel->isBlocksActions() ? GameMode::SURVIVAL : GameMode::ADVENTURE);
		if($this->duel->getConfig()->getMode()->isNoClientPredictionsOnStart()){
			$player->setNoClientPredictions();
		}
		$player->teleport($this->duel->getSpawnManager()->getSpawn($team));
		$player->setKnockBack($this->duel->getKnockBack());

		$kit = $this->duel->getConfig()->getMode()->toKit();
		$profile = $player->getProfile();
		$layout = $profile->getKitLayout($kit);
		if($layout === null){
			$this->duel->getKit()->applyTo($player);
		}else{
			$this->duel->getPlugin()->getKitEditorManager()->equipLayoutOnKit($layout, $kit)->applyTo($player);
		}

		if(count($this->players) === $this->duel->getTeamManager()->getPlayersPerTeam() * count($this->duel->getTeamManager()->getTeams())){
			$this->onAllPlayersJoined();
		}
	}

	private function onAllPlayersJoined() : void{
		foreach($this->players as $player){
			if(!$player->isConnected() || $player->getWorld() !== $this->duel->getWorldManager()->getWorld()){
				$this->duel->getPlugin()->getDuelManager()->closeDuel($this->duel);
				return;
			}
		}

		$this->duel->getOpponentManager()->onAllPlayersJoined();
		$this->duel->getKit()->addAdditionalStatistics($this->duel->getStatisticsManager(), $this->players);
		foreach($this->duel->getTeamManager()->getTeams() as $team){
			$team->initPlayedPlayers();
		}
		foreach($this->players as $player){
			$this->setPlayerNameTag($player);
			$this->duel->getPhaseManager()->getPhase()->setScoreboard($player);
			$player->getProfile()->onDuelPlay($this->duel->getType(), $this->duel->getConfig()->getMode());
		}
		$this->duel->getPhaseManager()->onUpdate();
	}

	public function removePlayer(CollapsePlayer $player) : void{
		unset($this->players[$player->getName()]);
		$player->setGame(null);
		$player->setTeam(null);
		$player->setKnockBack(null);
		$player->setBasicProperties($player->getGamemode());
		$this->duel->getPlugin()->getRankManager()->setPlayerNameTag($player);
	}

	public function addLoser(CollapsePlayer $player) : void{
		$player->setGamemode(GameMode::SPECTATOR);
		$this->removePlayer($player);
		$this->losers[$player->getXuid()] = $player;
	}

	public function broadcastMessage(Translatable $translation, bool $prefix = true) : void{
		foreach(array_merge($this->players, $this->losers) as $player){
			if(!($player instanceof CollapsePlayer || $player->isConnected())){
				continue;
			}
			$player->sendTranslatedMessage($translation, $prefix);
		}
	}

	public function onPlayerDied(CollapsePlayer $player) : void{
		$this->duel->getRecord()->saveInventory($player->getXuid(), $player->getInventory()->getContents());
		$this->duel->getRecord()->setPotionEffects($player->getXuid(), DuelRecord::formatPotionsEffects($player));
		$this->duel->getStatisticsManager()->get(GameStatistics::HUNGER)?->set($player, $player->getHungerManager()->getFood());

		$this->addLoser($player);
		$aliveTeams = $this->duel->getTeamManager()->getAliveTeams();
		if(count($aliveTeams) < 2){
			$key = array_key_first($aliveTeams);
			if(isset($aliveTeams[$key])){
				$aliveTeam = $aliveTeams[$key];
				$this->duel->getPlayerManager()->onVictory($aliveTeam);
			}
		}
	}

	public function onVictory(Team $team) : void{
		$record = $this->duel->getRecord();
		$record->setWinners(array_keys($team->getPlayedPlayers()));
		$record->setLosers(array_keys($this->losers));
		$statisticsManager = $this->duel->getStatisticsManager();
		foreach($this->players as $player){
			$statisticsManager->get(GameStatistics::HEALTH)?->set($player, $player->getHealth());
			$statisticsManager->get(GameStatistics::HUNGER)?->set($player, $player->getHungerManager()->getFood());
			$record->saveInventory($player->getXuid(), $player->getInventory()->getContents());
			$record->setPotionEffects($player->getXuid(), DuelRecord::formatPotionsEffects($player));
		}
		$record->setStatistics($statisticsManager->export());
		$record->setDurationEnabled(false);

		$winner = $this->duel->getType()->isSolo() ? $team->getPlayers()[array_key_first($team->getPlayers())] : $team;
		$loser = $this->duel->getOpponentManager()->getOpponent($winner);
		foreach(array_merge($this->players, $this->losers, $this->duel->getSpectatorManager()->getSpectators()) as $player){
			if(!$player instanceof CollapsePlayer || !$player->isConnected() || $player->getWorld() !== $this->duel->getWorldManager()->getWorld()){
				continue;
			}
			if($record->hasWinner($player->getXuid())){
				$player->getWorld()->addSound($player->getLocation(), new MinecraftSound(MinecraftSoundNames::RANDOM_ORB, 0.7), [$player]);
			}
			$player->sendTranslatedMessage(
				CollapseTranslationFactory::duels_match_results(
					$winner->getName(),
					$loser->getName()
				), false
			);
		}
		$this->duel->getPhaseManager()->setPhase(new PhaseEnd($this->duel));
		(new DuelVictoryEvent($this->duel))->call();
	}

	public function close() : void{
		$lobbyManager = $this->duel->getPlugin()->getLobbyManager();
		$hasWinners = !empty($this->duel->getRecord()->getWinners());
		foreach(array_merge($this->players, $this->losers) as $player){
			if(!$player instanceof CollapsePlayer || !$player->isConnected()){
				continue;
			}
			$this->removePlayer($player);
			$lobbyManager->sendToLobby($player);
			if($hasWinners){
				$player->sendForm(new PostMatchForm($player, $this->duel->getRecord()));
			}
		}
		$this->losers = [];
	}
}

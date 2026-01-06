<?php

declare(strict_types=1);

namespace collapse\game\duel\listener;

use collapse\game\duel\Duel;
use collapse\game\duel\DuelManager;
use collapse\game\duel\event\DuelVictoryEvent;
use collapse\game\duel\form\DuelRequestForm;
use collapse\game\duel\types\DuelType;
use collapse\game\event\BlockBreakGameEvent;
use collapse\game\event\BlockPlaceGameEvent;
use collapse\game\event\PlayerAttackPlayerGameEvent;
use collapse\game\event\PlayerDamageGameEvent;
use collapse\game\event\PlayerDeathGameEvent;
use collapse\game\event\PlayerKillPlayerGameEvent;
use collapse\game\kit\KitCollection;
use collapse\i18n\CollapseTranslationFactory;
use collapse\lobby\item\Duels;
use collapse\player\CollapsePlayer;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerEntityInteractEvent;
use function array_values;
use function number_format;

final readonly class DuelListener implements Listener{

	public function __construct(
		private DuelManager $duelManager
	){}

	/**
	 * @priority LOWEST
	 */
	public function handleDuelVictory(DuelVictoryEvent $event) : void{
		$duel = $event->getDuel();
		if($duel->getType() === DuelType::Ranked){
			$players = array_values($duel->getPlayerManager()->getPlayers());
			$losers = array_values($duel->getPlayerManager()->getLosers());

			if(empty($players) || empty($losers)){
				$this->duelManager->getRecordManager()->onMatchEnd($duel);
				return;
			}

			$winner = $players[0];
			$loser = $losers[0];
			$mode = $duel->getConfig()->getMode();
			$result = $this->duelManager->getEloCalculator()->calculate($winner->getProfile()->getDuelsElo($mode), $loser->getProfile()->getDuelsElo($mode));

			$duel->getRecord()->setEloUpdates([
				$winner->getXuid() => [
					'before' => $winner->getProfile()->getDuelsElo($mode),
					'gain' => $result->getWinnerGain(),
					'total' => $result->getWinner(),
				],
				$loser->getXuid() => [
					'before' => $loser->getProfile()->getDuelsElo($mode),
					'gain' => $result->getLoserGain(),
					'total' => $result->getLoser()
				]
			]);

			$duel->getPlayerManager()->broadcastMessage($msg = CollapseTranslationFactory::duels_elo_updates(
				$winner->getName() . ': ' . number_format($result->getWinner()) . ' (+' . $result->getWinnerGain() . ')',
				$loser->getName() . ': ' . number_format($result->getLoser()) . ' (' . $result->getLoserGain() . ')',
			), false);
			$duel->getSpectatorManager()->broadcastMessage($msg, false);
		}

		$this->duelManager->getRecordManager()->onMatchEnd($duel);
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerAttackPlayerGame(PlayerAttackPlayerGameEvent $event) : void{
		$duel = $event->getGame();
		if(!$duel instanceof Duel){
			return;
		}

		if($event->getPlayer()->getTeam() === $event->getAttacker()->getTeam()){
			$event->getSubEvent()->cancel();
			return;
		}

		$phase = $duel->getPhaseManager()->getPhase();
		$phase->handlePlayerAttackPlayer($event);
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerDamageGame(PlayerDamageGameEvent $event) : void{
		$duel = $event->getGame();
		if(!$duel instanceof Duel){
			return;
		}
		$phase = $duel->getPhaseManager()->getPhase();
		$phase->handlePlayerDamage($event);
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerDeathGame(PlayerDeathGameEvent $event) : void{
		$duel = $event->getGame();
		if(!$duel instanceof Duel){
			return;
		}
		$phase = $duel->getPhaseManager()->getPhase();
		$phase->handlePlayerDeath($event);
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerKillPlayerGame(PlayerKillPlayerGameEvent $event) : void{
		$duel = $event->getGame();
		if(!$duel instanceof Duel){
			return;
		}
		$phase = $duel->getPhaseManager()->getPhase();
		$phase->handlePlayerKillPlayer($event);
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerDropItem(PlayerDropItemEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		if($player->getGame() instanceof Duel && $event->getItem()->getNamedTag()->getByte(KitCollection::TAG_MAIN_WEAPON, 0)){
			$event->cancel();
			$player->sendTranslatedMessage(CollapseTranslationFactory::drop_main_weapon());
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handleBlockBreakGame(BlockBreakGameEvent $event) : void{
		$duel = $event->getGame();
		if(!$duel instanceof Duel){
			return;
		}
		$phase = $duel->getPhaseManager()->getPhase();
		$phase->handleBlockBreak($event);
	}

	/**
	 * @priority LOWEST
	 */
	public function handleBlockPlaceGame(BlockPlaceGameEvent $event) : void{
		$duel = $event->getGame();
		if(!$duel instanceof Duel){
			return;
		}
		$phase = $duel->getPhaseManager()->getPhase();
		$phase->handleBlockPlace($event);
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerEntityInteract(PlayerEntityInteractEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		$target = $event->getEntity();
		if(
			$target instanceof CollapsePlayer &&
			$this->duelManager->getPlugin()->getLobbyManager()->isInLobby($player) &&
			$this->duelManager->getPlugin()->getLobbyManager()->isInLobby($target) &&
			$player->getInventory()->getItemInHand() instanceof Duels
		){
			$player->sendForm(new DuelRequestForm($player, $target));
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handleBlockUpdate(BlockUpdateEvent $event) : void{
		$block = $event->getBlock();
		$world = $block->getPosition()->getWorld();
		foreach($this->duelManager->getDuels() as $duel){
			if($duel->getWorldManager()->getWorld() === $world && !$duel->getConfig()->getMode()->hasBlockUpdates()){
				$event->cancel();
			}
		}
	}
}

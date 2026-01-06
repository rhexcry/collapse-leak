<?php

declare(strict_types=1);

namespace collapse\game\ffa\listener;

use collapse\game\event\PlayerAttackPlayerGameEvent;
use collapse\game\event\PlayerDeathGameEvent;
use collapse\game\event\PlayerKillPlayerGameEvent;
use collapse\game\ffa\FreeForAllArena;
use collapse\game\ffa\FreeForAllManager;
use collapse\game\respawn\PlayerRespawnGameEvent;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\player\rank\Rank;
use collapse\resourcepack\Font;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\utils\TextFormat;

final readonly class FreeForAllListener implements Listener{

	public function __construct(
		private FreeForAllManager $freeForAllManager
	){}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerRespawnGame(PlayerRespawnGameEvent $event) : void{
		$arena = $event->getGame();
		if(!$arena instanceof FreeForAllArena){
			return;
		}

		$player = $event->getPlayer();
		$arena->getPlayerManager()->onPlayerRespawn($player);
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerDropItem(PlayerDropItemEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		if($player->getGame() instanceof FreeForAllArena){
			$event->cancel();
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerDeathGame(PlayerDeathGameEvent $event) : void{
		$game = $event->getGame();
		if(!$game instanceof FreeForAllArena){
			return;
		}

		$game->getPlayerManager()->onPlayerDie($event->getPlayer());
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerKillPlayerGame(PlayerKillPlayerGameEvent $event) : void{
		$game = $event->getGame();
		if(!$game instanceof FreeForAllArena){
			return;
		}

		$player = $event->getPlayer();
		$opponnent = $event->getKiller();

		$statisticsManager = $game->getOpponentManager()?->getStatistics($player);
		if($statisticsManager !== null){
			$killerName = $opponnent->getProfile()->getRank() === Rank::DEFAULT ? $opponnent->getNameWithRankColor() : Font::minecraftColorToUnicodeFont($opponnent->getNameWithRankColor());
			$playerName = $player->getProfile()->getRank() === Rank::DEFAULT ? $player->getNameWithRankColor() : Font::minecraftColorToUnicodeFont($player->getNameWithRankColor());
			$player->sendMessage(Font::SCOREBOARD_LINE . EOL .
				$killerName . TextFormat::RESET . TextFormat::GRAY . ' VS ' . $playerName . EOL .
				$statisticsManager->format($player, $opponnent, $player) . EOL .
				Font::SCOREBOARD_LINE,
				false);
			$opponnent->sendMessage(
				Font::SCOREBOARD_LINE . EOL .
				$killerName . TextFormat::RESET . TextFormat::GRAY . ' VS ' . $playerName . EOL .
				$statisticsManager->format($opponnent, $opponnent, $player) . EOL .
				Font::SCOREBOARD_LINE,
				false
			);
		}

		$statisticsManager = $game->getOpponentManager()?->getStatistics($player);
		if($event->getBroadcastMessage() !== null){
			$game->getPlayerManager()->broadcastMessage($event->getBroadcastMessage(), false);
			$event->setBroadcastMessage(null);
		}else{
			$game->getPlayerManager()->broadcastMessage(CollapseTranslationFactory::kill_messages_default_player(
				$game->createPlayerBroadcastTags($player, $statisticsManager),
				$game->createKillerBroadcastTags($opponnent, $statisticsManager)
			), false);
		}

		$game->getPlayerManager()->onPlayerKill($event->getPlayer(), $event->getKiller());
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerAttackPlayer(PlayerAttackPlayerGameEvent $event) : void{
		$game = $event->getGame();
		if(!$game instanceof FreeForAllArena){
			return;
		}

		$player = $event->getPlayer();
		$attacker = $event->getAttacker();

		if($player === $attacker){
			return;
		}

		$opponentManager = $game->getOpponentManager();
		if($opponentManager === null){
			return;
		}

		if($game->isCombat()){
			if($game->isAntiInterrupt()){
				$playerOpponent = $opponentManager->getOpponent($player);
				$attackerOpponent = $opponentManager->getOpponent($attacker);

				if($attackerOpponent !== null && $attackerOpponent !== $player){
					$event->getSubEvent()->cancel();
					return;
				}

				if($playerOpponent === null){
					$opponentManager->setInCombat($player, $attacker);
				}else{
					if($playerOpponent !== $attacker){
						$event->getSubEvent()->cancel();
						$attacker->sendTranslatedPopup(CollapseTranslationFactory::free_for_all_in_combat(
							$player->getNameWithRankColor(),
							$playerOpponent->getNameWithRankColor()
						));
					}else{
						$opponentManager->updateCombatTime($player);
					}
				}

			}else{
				$opponentManager->setInCombat($player, $attacker);
			}
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handleBlockUpdate(BlockUpdateEvent $event) : void{
		$world = $event->getBlock()->getPosition()->getWorld();
		foreach($this->freeForAllManager->getArenas() as $arena){
			if($arena->getConfig()->getSpawnLocation()->getWorld() === $world){
				$event->cancel();
				break;
			}
		}
	}
}

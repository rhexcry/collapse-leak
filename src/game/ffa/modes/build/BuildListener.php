<?php

declare(strict_types=1);

namespace collapse\game\ffa\modes\build;

use collapse\game\event\BlockBreakGameEvent;
use collapse\game\event\BlockPlaceGameEvent;
use collapse\game\event\PlayerDamageGameEvent;
use collapse\game\event\PlayerDeathGameEvent;
use collapse\game\event\PlayerKillPlayerGameEvent;
use collapse\i18n\CollapseTranslationFactory;
use pocketmine\block\Block;
use pocketmine\event\Listener;

final readonly class BuildListener implements Listener{

	/**
	 * @priority LOWEST
	 */
	public function handleBlockBreakGame(BlockBreakGameEvent $event) : void{
		$arena = $event->getGame();
		if(!$arena instanceof Build){
			return;
		}
		$block = $event->getSubEvent()->getBlock();
		if(!$arena->getBlockManager()->hasBlock($block)){
			$event->cancel();
			return;
		}
		$event->getSubEvent()->setDrops([]);
		$arena->getBlockManager()->onBlockBreak($block);
	}

	/**
	 * @priority LOWEST
	 */
	public function handleBlockPlaceGame(BlockPlaceGameEvent $event) : void{
		$arena = $event->getGame();
		if(!$arena instanceof Build){
			return;
		}
		$player = $event->getPlayer();
		$blockManager = $arena->getBlockManager();
		$item = $event->getSubEvent()->getItem();
		/** @var Block $block */
		foreach($event->getSubEvent()->getTransaction()->getBlocks() as [, , , $block]){
			if($arena->getSpawnBounds()->isVectorInside($block->getPosition())){
				$event->cancel();
				break;
			}
			$blockManager->onBlockPlace($player, $block, $item);
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerDamageGame(PlayerDamageGameEvent $event) : void{
		$arena = $event->getGame();
		if(!$arena instanceof Build){
			return;
		}
		if($arena->getSpawnBounds()->isVectorInside($event->getPlayer()->getPosition())){
			$event->getSubEvent()->cancel();
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerDeath(PlayerDeathGameEvent $event) : void{
		$arena = $event->getGame();
		if(!$arena instanceof Build){
			return;
		}

		$player = $event->getPlayer();
		$arena->getBlockManager()->onPlayerDie($player);

		$opponent = $arena->getOpponentManager()?->getOpponent($player);

		if($opponent !== null){
			/*if($arena->getRespawnManager()->hasRespawnTask($player)){
				return;
			}*/

			$event->setDeathMessage(CollapseTranslationFactory::kill_messages_default_player(
				$arena->createPlayerBroadcastTags($player, null),
				$arena->createKillerBroadcastTags($opponent, null)
			));
			$player->teleport($arena->getConfig()->getSpawnLocation());

			$arena->getPlayerManager()->onPlayerKill($player, $opponent);
			return;
		}

		$player->teleport($arena->getConfig()->getSpawnLocation());
		//$arena->getPlayerManager()->onPlayerDie($player);
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerKillPlayer(PlayerKillPlayerGameEvent $event) : void{
		$arena = $event->getGame();
		if(!$arena instanceof Build){
			return;
		}

		$arena->getBlockManager()->onPlayerDie($event->getPlayer());
	}
}

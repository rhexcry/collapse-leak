<?php

declare(strict_types=1);

namespace collapse\game\ffa\modes\crystal;

use collapse\game\event\BlockBreakGameEvent;
use collapse\game\event\BlockPlaceGameEvent;
use collapse\game\event\PlayerDeathGameEvent;
use collapse\game\event\PlayerKillPlayerGameEvent;
use collapse\i18n\CollapseTranslationFactory;
use collapse\resourcepack\Font;
use pocketmine\block\Block;
use pocketmine\entity\object\EndCrystal;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\Listener;

final readonly class CrystalListener implements Listener{

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerKillPlayer(PlayerKillPlayerGameEvent $event) : void{
		$game = $event->getGame();

		if(!$game instanceof Crystal){
			return;
		}

		$player = $event->getPlayer();
		$killer = $event->getKiller();

		$game->getBlockManager()->onPlayerDie($player);
		if($player === $killer){
			$opponent = $game->getOpponentManager()?->getOpponent($player);
			if($opponent !== null && $event->getCause() === EntityDamageEvent::CAUSE_ENTITY_EXPLOSION){
				$event->setBroadcastMessage(CollapseTranslationFactory::kill_messages_default_explode(
					$game->createPlayerBroadcastTags($player, null),
					$game->createKillerBroadcastTags($opponent, null)
				));
				$event->setKiller($opponent);
				return;
			}

			$event->setBroadcastMessage(CollapseTranslationFactory::kill_messages_default_unknown(Font::minecraftColorToUnicodeFont($player->getNameWithRankColor())));
			return;
		}

		if($event->getCause() === EntityDamageEvent::CAUSE_ENTITY_EXPLOSION){
			$event->setBroadcastMessage(CollapseTranslationFactory::kill_messages_default_explode(
				$game->createPlayerBroadcastTags($player, null),
				$game->createKillerBroadcastTags($killer, null)
			));
		}
	}

	public function handlePlayerDeathGame(PlayerDeathGameEvent $event) : void{
		$game = $event->getGame();
		if(!$game instanceof Crystal){
			return;
		}

		$player = $event->getPlayer();
		$game->getBlockManager()->onPlayerDie($player);
		if($event->getCause() === EntityDamageEvent::MODIFIER_PREVIOUS_DAMAGE_COOLDOWN){
			$event->setDeathMessage(CollapseTranslationFactory::kill_messages_default_unknown(Font::minecraftColorToUnicodeFont($player->getNameWithRankColor())));
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handleBlockBreakGame(BlockBreakGameEvent $event) : void{
		$arena = $event->getGame();
		if(!$arena instanceof Crystal){
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
		if(!$arena instanceof Crystal){
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

	public function handleEntityExplode(EntityExplodeEvent $event) : void{
		$entity = $event->getEntity();
		if(!$entity instanceof EndCrystal){
			return;
		}

		$event->setBlockList([]);
	}
}

<?php

declare(strict_types=1);

namespace collapse\game;

use collapse\cooldown\types\CooldownType;
use collapse\cooldown\types\EnderPearl;
use collapse\entity\CollapseEnderPearl as CollapseEnderPearlEntity;
use collapse\entity\CollapseSplashPotion as CollapseSplashPotionEntity;
use collapse\game\event\BlockBreakGameEvent;
use collapse\game\event\BlockPlaceGameEvent;
use collapse\game\event\PlayerAttackPlayerGameEvent;
use collapse\game\event\PlayerDamageGameEvent;
use collapse\game\event\PlayerDeathGameEvent;
use collapse\game\event\PlayerKillPlayerGameEvent;
use collapse\game\ffa\FreeForAllArena;
use collapse\game\ffa\modes\crystal\Crystal;
use collapse\game\statistics\GameStatistics;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\resourcepack\Font;
use pocketmine\block\Bed;
use pocketmine\entity\object\EndCrystal;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Armor;
use pocketmine\network\mcpe\protocol\types\InputMode;
use pocketmine\world\sound\XpCollectSound;
use collapse\item\default\CollapseSplashPotion as CollapseSplashPotionItem;
use collapse\item\default\CollapseEnderPearl as CollapseEnderPearlItem;

final class GameListener implements Listener{

	/** @var array<string, array<string, array{string, int}>> */
	private array $ongoingKillProcesses;

	public function __construct(
		private readonly Practice $plugin
	){
		$this->ongoingKillProcesses = [];
	}

	private function cleanupOngoingProcesses(string $gameHash) : void{
		$fiber = new \Fiber(function () use ($gameHash){
			if(!isset($this->ongoingKillProcesses[$gameHash])){
				return;
			}
			foreach($this->ongoingKillProcesses[$gameHash] as $uuid => $data){
				if(time() - $data[1] > 5){
					unset($this->ongoingKillProcesses[$gameHash][$uuid]);
				}
			}
		});
		$fiber->start();
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerExhaust(PlayerExhaustEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		if($player->getGame() !== null){
			$event->cancel();
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handleInventoryTransaction(InventoryTransactionEvent $event) : void{
		$transaction = $event->getTransaction();
		/** @var CollapsePlayer $player */
		$player = $transaction->getSource();
		if($player->getGame() !== null){
			foreach($transaction->getActions() as $action){
				if($action->getSourceItem() instanceof Armor){
					$event->cancel();
				}
			}
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerInteract(PlayerInteractEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		if($player->isInGame() && $event->getBlock() instanceof Bed){
			$event->cancel();
		}

		$item = $event->getItem();
		if($player->isInGame() && $player->getProfile()->getInputMode() === InputMode::TOUCHSCREEN){
			if($item instanceof CollapseSplashPotionItem || $item instanceof CollapseEnderPearlItem){
				$returnedItems = [];
				$item->onClickAir($player, $player->getDirectionVector(), $returnedItems);
			}
		}
	}

	/**
	 * @priority MONITOR
	 */
	public function handleEntityDamage(EntityDamageEvent $event) : void{
		if($event->isCancelled()){
			return;
		}
		$player = $event->getEntity();
		if($player instanceof CollapsePlayer && $player->getGame() !== null){
			if($event->getCause() === EntityDamageEvent::CAUSE_FALL && $player->getGame()->isFallDamageDisabled()){
				$event->cancel();
				return;
			}
			if($player->getGame()->isDamageDisabled()){
				$event->setBaseDamage(0);
				if($event->getModifier(EntityDamageEvent::MODIFIER_CRITICAL) > 0){
					$event->setModifier(0, EntityDamageEvent::MODIFIER_CRITICAL);
				}
			}
			$game = $player->getGame();
			if(($player->getHealth() - $event->getFinalDamage()) <= 0 || $event->getCause() === EntityDamageEvent::CAUSE_VOID){
				$event->cancel();
				$ev = new PlayerDeathGameEvent($game, $player, $event->getCause(), CollapseTranslationFactory::kill_messages_default_unknown(Font::minecraftColorToUnicodeFont($player->getNameWithRankColor())));
				$ev->call();
				if($game instanceof FreeForAllArena){
					$game->getPlayerManager()->broadcastMessage($ev->getDeathMessage(), false);
				}
			}else{
				(new PlayerDamageGameEvent($game, $player, $event))->call();
			}
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handleEntityRegainHealth(EntityRegainHealthEvent $event) : void{
		$player = $event->getEntity();
		if($player instanceof CollapsePlayer && $player->getGame() !== null){
			$statistics = $player->getGame()->getStatistics($player);
			$statistics?->get(GameStatistics::HEALTH_REGENERATED)?->add($player, $event->getAmount());
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handleBlockBreak(BlockBreakEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		if($player->getGame() !== null){
			if($player->getGame()->isBlocksActions()){
				$ev = new BlockBreakGameEvent($player->getGame(), $player, $event);
				$ev->call();
				if($ev->isCancelled()){
					$event->cancel();
				}
			}else{
				$event->cancel();
			}
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handleBlockPlace(BlockPlaceEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		if($player->getGame() !== null){
			if($player->getGame()->isBlocksActions()){
				$ev = new BlockPlaceGameEvent($player->getGame(), $player, $event);
				$ev->call();
				if($ev->isCancelled()){
					$event->cancel();
				}
			}else{
				$event->cancel();
			}
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handleEntityDamageByChild(EntityDamageByChildEntityEvent $event) : void{
		if($event->isCancelled()){
			return;
		}
		$player = $event->getEntity();
		if($player instanceof CollapsePlayer && $player->getGame() !== null){
			$attacker = $event->getChild()->getOwningEntity();
			if($attacker instanceof CollapsePlayer && $attacker->getGame() === $player->getGame()){
				$attacker->getWorld()->addSound($attacker->getLocation(), new XpCollectSound(), [$attacker]);
			}
		}
	}

	/**
	 * @priority MONITOR
	 */
	public function handleEntityDamageByEntity(EntityDamageByEntityEvent $event) : void{
		if($event->isCancelled()){
			return;
		}

		$player = $event->getEntity();
		$attacker = $event->getDamager();

		$isValidPlayer = $player instanceof CollapsePlayer && $player->getGame() !== null;
		$isValidAttacker = $attacker instanceof CollapsePlayer || $attacker instanceof EndCrystal;

		if(!$isValidPlayer || !$isValidAttacker){
			return;
		}

		$game = $player->getGame();

		if($attacker instanceof EndCrystal){
			$attacker = $attacker->getOwningEntity();
			if(!$attacker instanceof CollapsePlayer){
				$event->cancel();
				return;
			}
		}

		if($game !== $attacker->getGame()){
			$event->cancel();
			return;
		}

		$gameHash = spl_object_hash($game);
		$potentialVictimUuid = $attacker->getUniqueId()->toString();
		$killerData = $this->ongoingKillProcesses[$gameHash][$potentialVictimUuid] ?? null;
		if($killerData !== null && $killerData[0] === $player->getUniqueId()->toString()){
			$event->cancel();
			return;
		}

		if($game->isDamageDisabled()){
			$event->setBaseDamage(0);
			if($event->getModifier(EntityDamageEvent::MODIFIER_CRITICAL) > 0){
				$event->setModifier(0, EntityDamageEvent::MODIFIER_CRITICAL);
			}
		}

		$statistics = $game->getStatistics($player);
		if($statistics !== null){
			$statistics->get(GameStatistics::COMBO)?->set($player, 0);
			$statistics->get(GameStatistics::DAMAGE_DEALT)?->add($attacker, $event->getFinalDamage());
			$statistics->get(GameStatistics::HITS)?->add($attacker, 1);
			$statistics->get(GameStatistics::COMBO)?->add($attacker, 1);

			if($event->getModifier(EntityDamageEvent::MODIFIER_CRITICAL) > 0){
				$statistics->get(GameStatistics::CRITICAL_HITS)?->add($attacker, 1);
			}

			$currentCombo = $statistics->get(GameStatistics::COMBO)?->get($attacker) ?? 0;
			$maxCombo = $statistics->get(GameStatistics::MAX_COMBO)?->get($attacker) ?? 0;
			if($currentCombo > $maxCombo){
				$statistics->get(GameStatistics::MAX_COMBO)?->set($attacker, $currentCombo);
			}
		}

		$isLethalDamage = ($player->getHealth() - $event->getFinalDamage()) <= 0;

		if($isLethalDamage){
			$event->cancel();
			$this->handleLethalDamage($player, $attacker, $event, $game);
		}else{
			(new PlayerAttackPlayerGameEvent($game, $player, $attacker, $event))->call();
		}
	}

	private function handleLethalDamage(CollapsePlayer $player, CollapsePlayer $attacker, EntityDamageByEntityEvent $event, Game $game) : void{
		$gameHash = spl_object_hash($game);
		$this->cleanupOngoingProcesses($gameHash);
		$victimUuid = $player->getUniqueId()->toString();

		if($player === $attacker){
			$opponent = $game instanceof FreeForAllArena ? $game->getOpponentManager()?->getOpponent($player) : null;

			if($opponent !== null){
				$this->ongoingKillProcesses[$gameHash][$victimUuid] = [$opponent->getUniqueId()->toString(), time()];
				$ev = new PlayerKillPlayerGameEvent($game, $player, $opponent, $event->getCause());
				$ev->call();
				unset($this->ongoingKillProcesses[$gameHash][$victimUuid]);

				if($game instanceof FreeForAllArena && $ev->getBroadcastMessage() !== null){
					$game->getPlayerManager()->broadcastMessage($ev->getBroadcastMessage(), false);
				}
			}else{
				$ev = new PlayerDeathGameEvent($game, $player, $event->getCause());
				$ev->call();

				if($game instanceof FreeForAllArena && $ev->getDeathMessage() !== null){
					$game->getPlayerManager()->broadcastMessage($ev->getDeathMessage(), false);
				}
			}
		}else{
			$this->ongoingKillProcesses[$gameHash][$victimUuid] = [$attacker->getUniqueId()->toString(), time()];
			$ev = new PlayerKillPlayerGameEvent($game, $player, $attacker, $event->getCause());
			$ev->call();
			unset($this->ongoingKillProcesses[$gameHash][$victimUuid]);
		}
	}

	/**
	 * @priority MONITOR
	 */
	public function handleProjectileLaunch(ProjectileLaunchEvent $event) : void{
		if($event->isCancelled()){
			return;
		}
		$projectile = $event->getEntity();
		$owningEntity = $projectile->getOwningEntity();
		if($owningEntity instanceof CollapsePlayer){
			if($projectile instanceof CollapseEnderPearlEntity){
				if($owningEntity->getGame()?->isEnderPearlCooldown()){
					$cooldownManager = $this->plugin->getCooldownManager();
					if($cooldownManager->hasCooldown($owningEntity, CooldownType::EnderPearl)){
						/*$owningEntity->sendTranslatedMessage(CollapseTranslationFactory::cooldown_ender_pearl_active());*/
						$owningEntity->sendTranslatedPopup(CollapseTranslationFactory::cooldown_ender_pearl_active());
						$event->cancel();
						return;
					}
					$cooldownManager->addCooldown($owningEntity, new EnderPearl($owningEntity, $owningEntity->getGame() instanceof Crystal ? 2 : 15));
				}
			}elseif($projectile instanceof CollapseSplashPotionEntity && $owningEntity->getGame() !== null){
				$owningEntity->getGame()->getStatistics($owningEntity)?->get(GameStatistics::THROW_POTIONS)->add($owningEntity, 1);
			}
		}
	}

	/**
	 * @priority HIGHEST
	 */
	public function handlePlayerQuit(PlayerQuitEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		$game = $player->getGame();

		if($game === null){
			return;
		}

		if($game instanceof FreeForAllArena){
			$game->getRespawnManager()->cancelRespawn($player);

			$opponentManager = $game->getOpponentManager();

			if($opponentManager === null || ($opponnent = $opponentManager->getOpponent($player)) === null){
				$game->onPlayerLeave($player);
				return;
			}

			$ev = new PlayerKillPlayerGameEvent($game, $player, $opponnent, EntityDamageEvent::CAUSE_ENTITY_ATTACK);
			$ev->call();

			$game->removePlayer($player);
			return;
		}

		$player->getGame()?->onPlayerLeave($player);
	}
}
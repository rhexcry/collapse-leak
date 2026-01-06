<?php

declare(strict_types=1);

namespace collapse\game\duel\modes\fireball;

use collapse\game\duel\entity\Fireball;
use collapse\game\duel\entity\TNT;
use collapse\game\duel\modes\basic\BedsEventListener;
use collapse\game\respawn\PlayerRespawnGameEvent;
use collapse\game\respawn\PlayerRespawnUpdateEvent;
use collapse\game\respawn\PlayerStartRespawnEvent;
use collapse\i18n\CollapseTranslationFactory;
use collapse\world\explosion\FireballFightExplosion;
use pocketmine\block\Bed;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\entity\EntityPreExplodeEvent;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\Listener;
use pocketmine\player\GameMode;
use pocketmine\world\Position;

final class FireballFightListener implements Listener{
	use BedsEventListener;

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerRespawnGame(PlayerRespawnGameEvent $event) : void{
		$duel = $event->getGame();
		if(!$duel instanceof FireballFight){
			return;
		}
		$player = $event->getPlayer();
		if($player->getTeam() === null){
			return;
		}
		$player->teleport($duel->getSpawnManager()->getSpawn($player->getTeam()));
		$player->setBasicProperties(GameMode::SURVIVAL);

		$kit = $duel->getConfig()->getMode()->toKit();
		$profile = $player->getProfile();
		$layout = $profile->getKitLayout($kit);
		if($layout === null){
			$duel->getKit()->applyTo($player);
		}else{
			$duel->getPlugin()->getKitEditorManager()->equipLayoutOnKit($layout, $kit)->applyTo($player);
		}

		$player->sendTranslatedTitle(CollapseTranslationFactory::respawn_base_title());
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerRespawnUpdate(PlayerRespawnUpdateEvent $event) : void{
		$player = $event->getPlayer();
		if(!$player->getGame() instanceof FireballFight){
			return;
		}
		$player->sendTranslatedTitle(
			CollapseTranslationFactory::respawn_base_progress_title(),
			CollapseTranslationFactory::respawn_base_progress_subtitle((string) $event->getCountdown()),
			0,
			20,
			0
		);
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerStartRespawn(PlayerStartRespawnEvent $event) : void{
		$player = $event->getPlayer();
		$duel = $player->getGame();
		if(!$duel instanceof FireballFight){
			return;
		}
		$player->setBasicProperties(GameMode::SPECTATOR);
		$player->setHasBlockCollision(true);
		$player->setAllowFlight(true);
		$player->setFlying(true);
		if($player->getTeam() !== null){
			$player->teleport($duel->getSpawnManager()->getSpawn($player->getTeam()));
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handleProjectileHitBlock(ProjectileHitBlockEvent $event) : void{
		$fireball = $event->getEntity();
		if(!$fireball instanceof Fireball){
			return;
		}
		$owningEntity = $fireball->getOwningEntity();
		$offset = 0;
		if(($owningEntity?->getLocation()->getY() - $event->getBlockHit()->getPosition()->getY()) == 1.0 && $owningEntity?->isOnGround()){ //HACK
			$offset = 1;
		}
		$ev = new EntityPreExplodeEvent($fireball, 2);
		$ev->call();
		if(!$ev->isCancelled()){
			$explosion = new FireballFightExplosion(Position::fromObject($fireball->getLocation()->add(0, ($fireball->getSize()->getHeight() / 2) - $offset, 0), $fireball->getWorld()), $ev->getRadius(), $fireball);
			if($ev->isBlockBreaking()){
				$explosion->explodeA();
			}
			$explosion->explodeB();
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handleProjectileHitEntity(ProjectileHitEntityEvent $event) : void{
		$fireball = $event->getEntity();
		if(!$fireball instanceof Fireball){
			return;
		}
		$ev = new EntityPreExplodeEvent($fireball, 2);
		$ev->call();
		if(!$ev->isCancelled()){
			$explosion = new FireballFightExplosion(Position::fromObject($fireball->getLocation()->add(0, $fireball->getSize()->getHeight() / 2, 0), $fireball->getWorld()), $ev->getRadius(), $fireball);
			if($ev->isBlockBreaking()){
				$explosion->explodeA();
			}
			$explosion->explodeB();
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handleEntityExplode(EntityExplodeEvent $event) : void{
		$entity = $event->getEntity();
		if(!($entity instanceof Fireball || $entity instanceof TNT)){
			return;
		}
		$duel = $entity->getGame();
		if(!$duel instanceof FireballFight){
			$event->cancel();
			return;
		}
		$blockManager = $duel->getBlockManager();
		$blockList = $event->getBlockList();
		foreach($blockList as $index => $block){
			if(!$blockManager->canBreakBlock($block) || $block instanceof Bed){
				unset($blockList[$index]);
			}
		}
		$event->setBlockList($blockList);
	}
}

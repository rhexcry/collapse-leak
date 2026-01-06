<?php

declare(strict_types=1);

namespace collapse\system\anticheat;

use collapse\player\CollapsePlayer;
use collapse\player\rank\Rank;
use collapse\system\anticheat\event\AnticheatKickEvent;
use collapse\utils\BlockUtils;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityMotionEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerJumpEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\Server;

final class AnticheatListener implements Listener{

	public function __construct(private readonly AnticheatManager $anticheatManager){
	}

	public function handleAnticheatKick(AnticheatKickEvent $event) : void{
		$player = $event->getSession()->getPlayer();
		if($player === null){
			return;
		}

		if($player->getProfile()?->getRank() === Rank::OWNER){
			$event->cancel();
		}
	}

	public function handlePlayerQuit(PlayerQuitEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		$session = AnticheatSession::from($player);
		AnticheatSession::destroy($session);
	}

	public function handleDataPacketReceive(DataPacketReceiveEvent $event) : void{
		/** @var DataPacket $packet */
		$packet = $event->getPacket();
		/** @var CollapsePlayer $player */
		$player = $event->getOrigin()->getPlayer();

		if($player === null){
			return;
		}

		if(!$player->isConnected() && !$player->spawned){
			return;
		}

		$playerSession = AnticheatSession::from($player);

		foreach($this->anticheatManager->getAllChecks() as $check){
			$check->check($packet, $playerSession);
		}
	}

	public function onPlayerMove(PlayerMoveEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		$playerSession = AnticheatSession::from($player);

		if(!$player->isConnected() && !$player->spawned){
			return;
		}
		if($playerSession->getPlayer() === null){
			return;
		}

		foreach($this->anticheatManager->getAllChecks() as $check){
			$check->checkEvent($event, $playerSession);
		}

		$playerSession->setOnGround(BlockUtils::isOnGround($event->getTo(), 0) || BlockUtils::isOnGround($event->getTo(), 1));
		if($playerSession->isOnGround()){
			$playerSession->setLastGroundY($player->getPosition()->getY());
		}else{
			$playerSession->setLastNoGroundY($player->getPosition()->getY());
		}

		$playerSession->setOnAdhesion(BlockUtils::isOnAdhesion($event->getTo(), 0));
		$playerSession->setLastMoveTick((double) Server::getInstance()->getTick());
	}

	public function onMotion(EntityMotionEvent $event) : void{
		$entity = $event->getEntity();
		if(!$entity instanceof CollapsePlayer){
			return;
		}
		$playerSession = AnticheatSession::from($entity);
		if($playerSession->getPlayer() === null){
			return;
		}

		if(!$playerSession->getPlayer()->isConnected() && !$playerSession->getPlayer()->spawned){
			return;
		}

		$playerSession->getMotion()->x += abs($event->getVector()->getX());
		$playerSession->getMotion()->z += abs($event->getVector()->getZ());
	}

	public function onEntityTeleport(EntityTeleportEvent $event) : void{
		if(($entity = $event->getEntity()) instanceof CollapsePlayer){
			return;
		}

		$playerSession = AnticheatSession::from($entity);

		if($playerSession->getPlayer() === null){
			return;
		}

		if(!$playerSession->getPlayer()->isConnected() && !$playerSession->getPlayer()->spawned){
			return;
		}

		$playerSession->setTeleportTicks(microtime(true));
	}

	public function onPlayerJump(PlayerJumpEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		$playerSession = AnticheatSession::from($player);
		if($playerSession->getPlayer() === null){
			return;
		}

		if(!$player->isConnected() && !$player->spawned){
			return;
		}

		$playerSession->setJumpTicks(microtime(true));
	}

	public function onPlayerJoin(PlayerJoinEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		$playerSession = AnticheatSession::from($player);
		if(!$player->isConnected() && !$player->spawned){
			return;
		}

		if($playerSession->getPlayer() === null){
			return;
		}

		$playerSession->setJoinedAtTheTime(microtime(true));
	}

	public function onEntityDamage(EntityDamageEvent $event) : void{
		foreach($this->anticheatManager->getAllChecks() as $check){
			$check->checkJustEvent($event);
		}

		if(($player = $event->getEntity()) instanceof CollapsePlayer){
			$playerSession = AnticheatSession::from($player);

			if(
				$event->getCause() === EntityDamageEvent::CAUSE_ENTITY_ATTACK ||
				$event->getCause() === EntityDamageEvent::CAUSE_PROJECTILE ||
				$event->getCause() === EntityDamageEvent::CAUSE_SUFFOCATION ||
				$event->getCause() === EntityDamageEvent::CAUSE_VOID ||
				$event->getCause() === EntityDamageEvent::CAUSE_FALLING_BLOCK
			){
				return;
			}
			$playerSession->setHurtTicks(microtime(true));
		}
	}

	public function onEntityDamageByEntity(EntityDamageByEntityEvent $event) : void{
		$cause = $event->getCause();
		$entity = $event->getEntity();
		if($entity === null){
			return;
		}

		$damager = $event->getDamager();
		if($damager === null){
			return;
		}
		if(!$damager instanceof CollapsePlayer){
			return;
		}

		$playerSession = AnticheatSession::from($damager);
		if($playerSession->getPlayer() === null){
			return;
		}

		if(!$playerSession->getPlayer()->isConnected() && !$playerSession->getPlayer()->spawned){
			return;
		}

		foreach($this->anticheatManager->getAllChecks() as $check){
			$check->checkJustEvent($event);
		}

		if($cause === EntityDamageEvent::CAUSE_ENTITY_ATTACK){
			if($entity instanceof CollapsePlayer){
				AnticheatSession::from($entity)->setAttackTicks(microtime(true));
			}

			$playerSession->setAttackTicks(microtime(true));
		}

		if($cause === EntityDamageEvent::CAUSE_ENTITY_EXPLOSION || $cause === EntityDamageEvent::CAUSE_BLOCK_EXPLOSION){
			AnticheatSession::from($entity)->setAttackTicks(microtime(true));
		}
	}

	public function onProjectileHit(ProjectileHitEvent $event) : void{
		$projectile = $event->getEntity();
		$player = $projectile->getOwningEntity();

		if($player !== null && $player instanceof CollapsePlayer){
			$playerSession = AnticheatSession::from($player);
			if($playerSession->getPlayer() === null){
				return;
			}

			$playerSession->setProjectileAttackTicks(microtime(true));
		}
	}

	public function onEntityShootBowEvent(EntityShootBowEvent $event) : void{
		$player = $event->getEntity();
		if(!$player instanceof CollapsePlayer){
			return;
		}

		$playerSession = AnticheatSession::from($player);
		if($playerSession->getPlayer() === null){
			return;
		}

		if(!$player->isConnected() && !$player->spawned){
			return;
		}

		$playerSession->setBowShotTicks(microtime(true));
		foreach($this->anticheatManager->getAllChecks() as $check){
			$check->checkEvent($event, $playerSession);
		}
	}
}
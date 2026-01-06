<?php

declare(strict_types=1);

namespace collapse\entity;

use collapse\game\ffa\FreeForAllArena;
use collapse\player\CollapsePlayer;
use pocketmine\block\BlockTypeIds;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\entity\NeverSavedWithChunkEntity;
use pocketmine\entity\projectile\EnderPearl;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\math\Vector3;
use pocketmine\math\VoxelRayTrace;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\timings\Timings;
use pocketmine\world\particle\EndermanTeleportParticle;
use pocketmine\world\sound\EndermanTeleportSound;
use function atan2;
use function sqrt;
use const M_PI;
use const PHP_INT_MAX;

final class CollapseEnderPearl extends EnderPearl implements NeverSavedWithChunkEntity{
	use CollapseVisibilityEntityTrait;

	protected function getInitialGravity() : float{
		return 0.065;
	}

	protected function getInitialDragMultiplier() : float{
		return 0.0085;
	}

	protected function onHit(ProjectileHitEvent $event) : void{
		$owner = $this->getOwningEntity();
		if($owner !== null && $owner->getWorld() === $this->getWorld()){
			$targets = $this->getTargets();
			$this->getWorld()->addParticle($origin = $owner->getPosition(), new EndermanTeleportParticle(), $targets);
			$this->getWorld()->addSound($origin, new EndermanTeleportSound(), $targets);
			if($owner instanceof CollapsePlayer){
				$owner->setPosition($target = $event->getRayTraceResult()->getHitVector());
				$location = $owner->getLocation();
				$owner->sendPosition($target, $location->yaw, $location->pitch, mode: MovePlayerPacket::MODE_TELEPORT);
				foreach($owner->getViewers() as $viewer){
					$viewer->getNetworkSession()->sendDataPacket(MovePlayerPacket::simple(
						$owner->getId(),
						$owner->getOffsetPosition($target),
						$location->pitch,
						$location->yaw,
						$location->yaw,
						MovePlayerPacket::MODE_TELEPORT,
						$owner->onGround,
						0,
						0
					));
				}
			}else{
				$owner->teleport($target = $event->getRayTraceResult()->getHitVector());
			}
			$this->getWorld()->addSound($target, new EndermanTeleportSound(), $targets);
		}
	}

	public function canCollideWith(Entity $entity) : bool{
		$owningEntity = $this->getOwningEntity();
		if($entity instanceof CollapsePlayer && $owningEntity instanceof CollapsePlayer){
			$game = $owningEntity->getGame();
			if($game instanceof FreeForAllArena && $game->isAntiInterrupt() && !($entity === $owningEntity || $game->getOpponentManager()->getOpponent($owningEntity) === $entity)){
				return false;
			}
		}
		return parent::canCollideWith($entity);
	}

	protected function move(float $dx, float $dy, float $dz) : void{
		$this->blocksAround = null;

		Timings::$projectileMove->startTiming();
		Timings::$projectileMoveRayTrace->startTiming();

		$start = $this->location->asVector3();
		$end = $start->add($dx, $dy, $dz);

		$hitResult = null;
		$world = $this->getWorld();
		$nonBarrierBlock = null;
		foreach(VoxelRayTrace::betweenPoints($start, $end) as $vector3){
			$block = $world->getBlockAt($vector3->x, $vector3->y, $vector3->z);
			$blockHitResult = $this->calculateInterceptWithBlock($block, $start, $end);
			if($blockHitResult !== null){
				$end = $blockHitResult->hitVector;
				if($nonBarrierBlock !== null && ($block->getTypeId() === BlockTypeIds::INVISIBLE_BEDROCK || $block->getTypeId() === BlockTypeIds::BARRIER)){
					$end = $nonBarrierBlock;
				}
				$hitResult = [$block, $blockHitResult];
				break;
			}
			$nonBarrierBlock = clone $vector3;
		}

		$entityDistance = PHP_INT_MAX;

		$newDiff = $end->subtractVector($start);
		foreach($world->getCollidingEntities($this->boundingBox->addCoord($newDiff->x, $newDiff->y, $newDiff->z)->expand(1, 1, 1), $this) as $entity){
			if($entity->getId() === $this->getOwningEntityId() && $this->ticksLived < 5){
				continue;
			}

			$entityBB = $entity->boundingBox->expandedCopy(0.3, 0.3, 0.3);
			$entityHitResult = $entityBB->calculateIntercept($start, $end);

			if($entityHitResult === null){
				continue;
			}

			$distance = $this->location->distanceSquared($entityHitResult->hitVector);

			if($distance < $entityDistance){
				$entityDistance = $distance;
				$hitResult = [$entity, $entityHitResult];
				$end = $entityHitResult->hitVector;
			}
		}

		Timings::$projectileMoveRayTrace->stopTiming();

		$this->location = Location::fromObject(
			$end,
			$this->location->world,
			$this->location->yaw,
			$this->location->pitch
		);
		$this->recalculateBoundingBox();

		if($hitResult !== null){
			[$objectHit, $rayTraceResult] = $hitResult;
			if($objectHit instanceof Entity){
				$ev = new ProjectileHitEntityEvent($this, $rayTraceResult, $objectHit);
				$specificHitFunc = fn() => $this->onHitEntity($objectHit, $rayTraceResult);
			}else{
				$ev = new ProjectileHitBlockEvent($this, $rayTraceResult, $objectHit);
				$specificHitFunc = fn() => $this->onHitBlock($objectHit, $rayTraceResult);
			}

			$ev->call();
			$this->onHit($ev);
			$specificHitFunc();

			$this->isCollided = $this->onGround = true;
			$this->motion = Vector3::zero();
		}else{
			$this->isCollided = $this->onGround = false;
			$this->blockHit = null;

			//recompute angles...
			$f = sqrt(($this->motion->x ** 2) + ($this->motion->z ** 2));
			$this->setRotation(
				atan2($this->motion->x, $this->motion->z) * 180 / M_PI,
				atan2($this->motion->y, $f) * 180 / M_PI
			);
		}

		$world->onEntityMoved($this);
		$this->checkBlockIntersections();

		Timings::$projectileMove->stopTiming();
	}
}

<?php

declare(strict_types=1);

namespace collapse\entity;

use collapse\game\ffa\FreeForAllArena;
use collapse\player\CollapsePlayer;
use pocketmine\block\BlockTypeTags;
use pocketmine\block\VanillaBlocks;
use pocketmine\color\Color;
use pocketmine\entity\effect\InstantEffect;
use pocketmine\entity\Entity;
use pocketmine\entity\Living;
use pocketmine\entity\NeverSavedWithChunkEntity;
use pocketmine\entity\projectile\SplashPotion;
use pocketmine\event\entity\ProjectileHitBlockEvent;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\item\PotionType;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\world\particle\PotionSplashParticle;
use pocketmine\world\sound\PotionSplashSound;
use function count;
use function in_array;
use function round;

final class CollapseSplashPotion extends SplashPotion implements NeverSavedWithChunkEntity{
	use CollapseVisibilityEntityTrait;

	private ?array $customColors = null;

	protected function getInitialGravity() : float{
		return 0.06;
	}

	protected function getInitialDragMultiplier() : float{
		return 0.01;
	}

	public function getCustomColors() : array{
		return $this->customColors;
	}

	public function setCustomColors(array $customColors) : void{
		$this->customColors = $customColors;
	}

	protected function onHit(ProjectileHitEvent $event) : void{
		$effects = $this->getPotionEffects();
		$hasEffects = true;

		if(count($effects) === 0){
			$particle = new PotionSplashParticle($this->customColors === null ? PotionSplashParticle::DEFAULT_COLOR() : Color::mix(...$this->customColors));
			$hasEffects = false;
		}else{
			if($this->customColors === null){
				$colors = [];
				foreach($effects as $effect){
					$level = $effect->getEffectLevel();
					for($j = 0; $j < $level; ++$j){
						$colors[] = $effect->getColor();
					}
				}
			}else{
				$colors = $this->customColors;
			}
			$particle = new PotionSplashParticle(Color::mix(...$colors));
		}

		$targets = $this->getTargets();
		$this->getWorld()->addParticle($this->location, $particle, $targets);
		$this->broadcastSound(new PotionSplashSound(), $targets);

		$owningEntity = $this->getOwningEntity();
		$availableEntities = null;
		if($owningEntity instanceof CollapsePlayer && $owningEntity->getGame() !== null){
			$game = $owningEntity->getGame();
			if($game instanceof FreeForAllArena){
				if(($opponent = $game->getOpponentManager()?->getOpponent($owningEntity)) !== null){
					$availableEntities = [$owningEntity->getId(), $opponent->getId()];
				}
			}
		}
		if($hasEffects){
			if(!$this->willLinger()){
				foreach($this->getWorld()->getCollidingEntities($this->boundingBox->expandedCopy(4.125, 2.125, 4.125), $this) as $entity){
					if($entity instanceof Living){
						if($availableEntities !== null && !in_array($entity->getId(), $availableEntities, true)){
							continue;
						}

						if($event instanceof ProjectileHitEntityEvent){
							$entityHit = $event->getEntityHit();
							if($entityHit !== $entity && $entityHit->getLocation()->distance($entity->getLocation()) > 1){
								continue;
							}
						}

						$distanceSquared = $entity->getEyePos()->distanceSquared($this->location);
						if($distanceSquared > 12){
							continue;
						}

						if($event instanceof ProjectileHitEntityEvent && $entity === $event->getEntityHit()){
							$potency = 1.0993;
						}else{
							$potency = 0.9593;
						}

						foreach($this->getPotionEffects() as $effect){
							if(!($effect->getType() instanceof InstantEffect)){
								$newDuration = (int) round($effect->getDuration() * 0.75 * $potency);
								if($newDuration < 20){
									continue;
								}
								$effect->setDuration($newDuration);
								$entity->getEffects()->add($effect);
							}else{
								$effect->getType()->applyEffect($entity, $effect, $potency, $this);
							}
						}
					}
				}
			}
		}elseif($event instanceof ProjectileHitBlockEvent && $this->getPotionType() === PotionType::WATER){
			$blockIn = $event->getBlockHit()->getSide($event->getRayTraceResult()->getHitFace());

			if($blockIn->hasTypeTag(BlockTypeTags::FIRE)){
				$this->getWorld()->setBlock($blockIn->getPosition(), VanillaBlocks::AIR());
			}
			foreach($blockIn->getHorizontalSides() as $horizontalSide){
				if($horizontalSide->hasTypeTag(BlockTypeTags::FIRE)){
					$this->getWorld()->setBlock($horizontalSide->getPosition(), VanillaBlocks::AIR());
				}
			}
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

	protected function syncNetworkData(EntityMetadataCollection $properties) : void{
		parent::syncNetworkData($properties);
		$properties->setFloat(EntityMetadataProperties::SCALE, 0.55);
	}
}

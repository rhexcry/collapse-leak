<?php

declare(strict_types=1);

namespace collapse\entity;

use collapse\entity\animation\FishHookTeaseAnimation;
use collapse\game\ffa\FreeForAllArena;
use collapse\player\CollapsePlayer;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\entity\NeverSavedWithChunkEntity;
use pocketmine\entity\projectile\Projectile;
use pocketmine\math\RayTraceResult;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\utils\Random;
use function intval;
use function microtime;
use function sqrt;

final class FishingHook extends Projectile implements NeverSavedWithChunkEntity{

	public Random $random;

	private ?Entity $caughtEntity = null;

	public function __construct(Location $location, ?Entity $owner = null, ?CompoundTag $nbt = null){
		$this->random = new Random(intval(microtime(true) * 1000));
		parent::__construct($location, $owner, $nbt);
	}

	public static function getNetworkTypeId() : string{
		return EntityIds::FISHING_HOOK;
	}

	protected function getInitialGravity() : float{
		return 0.09;
	}

	protected function getInitialDragMultiplier() : float{
		return 0.05;
	}

	protected function getInitialSizeInfo() : EntitySizeInfo{
		return new EntitySizeInfo(0.15, 0.15);
	}

	public function canSaveWithChunk() : bool{
		return false;
	}

	public function getResultDamage() : int{
		return 0;
	}

	public function handleHookCasting(float $x, float $y, float $z, float $f1, float $f2) : void{
		$f = sqrt($x * $x + $y * $y + $z * $z);
		$x = $x / $f;
		$y = $y / $f;
		$z = $z / $f;
		$x = $x + $this->random->nextSignedFloat() * 0.0075 * $f2;
		$y = $y + $this->random->nextSignedFloat() * 0.0075 * $f2;
		$z = $z + $this->random->nextSignedFloat() * 0.0075 * $f2;
		$x = $x * $f1;
		$y = $y * $f1;
		$z = $z * $f1;
		$this->motion->x += $x;
		$this->motion->y += $y;
		$this->motion->z += $z;
	}

	public function handleHookRetraction() : void{
		if($this->caughtEntity !== null){
			$this->broadcastAnimation(new FishHookTeaseAnimation($this));
		}
		$owningEntity = $this->getOwningEntity();
		if($owningEntity instanceof CollapsePlayer && $owningEntity->getFishingHook() === $this){
			$owningEntity->setFishingHook(null);
		}
		if(!$this->isFlaggedForDespawn()){
			$this->flagForDespawn();
		}
	}

	public function canCollideWith(Entity $entity) : bool{
		$owningEntity = $this->getOwningEntity();
		if($entity instanceof CollapsePlayer && $owningEntity instanceof CollapsePlayer){
			$game = $owningEntity->getGame();
			if($game instanceof FreeForAllArena && $game->isAntiInterrupt() && !($entity === $owningEntity || $game->getOpponentManager()->getOpponent($owningEntity) === $entity)){
				return false;
			}
			if($owningEntity->getTeam() !== null && $owningEntity->getTeam() === $entity->getTeam()){
				return false;
			}
		}
		return parent::canCollideWith($entity);
	}

	public function onHitEntity(Entity $entityHit, RayTraceResult $hitResult) : void{
		parent::onHitEntity($entityHit, $hitResult);
		$this->caughtEntity = $entityHit;
		$this->handleHookRetraction();
	}
}

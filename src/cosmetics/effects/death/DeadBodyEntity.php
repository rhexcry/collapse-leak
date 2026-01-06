<?php

declare(strict_types=1);

namespace collapse\cosmetics\effects\death;

use pocketmine\entity\Human;
use pocketmine\entity\NeverSavedWithChunkEntity;
use pocketmine\event\entity\EntityDamageEvent;
use function abs;
use function floatval;

final class DeadBodyEntity extends Human implements NeverSavedWithChunkEntity{

	public function canSaveWithChunk() : bool{
		return false;
	}

	public function onUpdate(int $currentTick) : bool{
		$update = parent::onUpdate($currentTick);
		if($update){
			if($this->hasMovementUpdate()){
				$this->tryChangeMovement();

				$this->motion = $this->motion->withComponents(
					abs($this->motion->x) <= self::MOTION_THRESHOLD ? 0 : null,
					abs($this->motion->y) <= self::MOTION_THRESHOLD ? 0 : null,
					abs($this->motion->z) <= self::MOTION_THRESHOLD ? 0 : null
				);

				if(floatval($this->motion->x) !== 0.0 || floatval($this->motion->y) !== 0.0 || floatval($this->motion->z) !== 0.0){
					$this->move($this->motion->x, $this->motion->y, $this->motion->z);
				}

				$this->forceMovementUpdate = false;
			}

			$this->updateMovement();
		}
		return $update;
	}

	public function attack(EntityDamageEvent $source) : void{
	}
}

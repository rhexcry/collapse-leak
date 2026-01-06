<?php

declare(strict_types=1);

namespace collapse\hologram;

use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\world\ChunkLoader;

final class HologramEntity extends Entity implements ChunkLoader{

	protected function initEntity(CompoundTag $nbt) : void{
		$this->setSilent();
		$this->setNoClientPredictions();
		$this->setNameTagAlwaysVisible();
		$this->setNameTagVisible();
	}

	public function getInitialSizeInfo() : EntitySizeInfo{
		return new EntitySizeInfo(0, 0);
	}

	public function getInitialDragMultiplier() : float{
		return 0.0;
	}

	public function getInitialGravity() : float{
		return 0.0;
	}

	public static function getNetworkTypeId() : string{
		return EntityIds::SLIME;
	}

	public function attack(EntityDamageEvent $source) : void{
	}

	public function canSaveWithChunk() : bool{
		return false;
	}

	public function syncNetworkData(EntityMetadataCollection $properties) : void{
		parent::syncNetworkData($properties);
		$properties->setFloat(EntityMetadataProperties::SCALE, 0.0);
	}
}

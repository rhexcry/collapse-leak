<?php

declare(strict_types=1);

namespace collapse\game\ffa;

use collapse\game\ffa\types\FreeForAllMode;
use pocketmine\entity\Location;

final readonly class FreeForAllConfig{

	public function __construct(
		private FreeForAllMode $mode,
		private Location $spawnLocation,
		private array $extraData
	){}

	public function getMode() : FreeForAllMode{
		return $this->mode;
	}

	public function getSpawnLocation() : Location{
		return $this->spawnLocation;
	}

	public function getExtraData() : array{
		return $this->extraData;
	}
}

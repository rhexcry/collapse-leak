<?php

declare(strict_types=1);

namespace collapse\game\duel;

use collapse\game\duel\types\DuelMode;
use pocketmine\entity\Location;

final readonly class DuelConfig{

	/**
	 * @param Location[] $spawnLocations
	 */
	public function __construct(
		private string $name,
		private DuelMode $mode,
		private string $mapPath,
		private array $spawnLocations
	){}

	public function getName() : string{
		return $this->name;
	}

	public function getMode() : DuelMode{
		return $this->mode;
	}

	public function getMapPath() : string{
		return $this->mapPath;
	}

	public function getSpawnLocations() : array{
		return $this->spawnLocations;
	}
}

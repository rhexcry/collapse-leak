<?php

declare(strict_types=1);

namespace collapse\game\duel\modes\skywars\island\config;

use collapse\game\duel\modes\skywars\island\IslandBounds;
use collapse\game\duel\modes\skywars\island\IslandType;

final readonly class IslandConfig{

	public function __construct(
		private string $id,
		private IslandBounds $bounds,
		private IslandType $islandType,
		private array $chestPositions = []
	){}

	public function getId() : string{
		return $this->id;
	}

	public function getBounds() : IslandBounds{
		return $this->bounds;
	}

	public function getChestPositions() : array{
		return $this->chestPositions;
	}

	public function getType() : IslandType{
		return $this->islandType;
	}
}

<?php

declare(strict_types=1);

namespace collapse\game\duel\modes\skywars\islanmsdndn\config;

use collapse\game\duel\modes\skywars\islanmsdndn\IslandType;
use collapse\utils\SerializableVector3;

final readonly class IslandConfig{

	public function __construct(
		public string              $id,
		public IslandType          $type,
		public SerializableVector3 $minBounds,
		public SerializableVector3 $maxBounds,
		public array               $spawnPoints = [],
		public array               $chestPositions = [],
		public array               $generatorPositions = []
	){
	}
}

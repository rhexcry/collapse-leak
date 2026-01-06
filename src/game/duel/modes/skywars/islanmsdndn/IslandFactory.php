<?php

declare(strict_types=1);

namespace collapse\game\duel\modes\skywars\islanmsdndn;

use collapse\game\duel\modes\skywars\islanmsdndn\config\IslandConfig;
use collapse\utils\SerializableVector3;

final class IslandFactory{

	public function createFromConfig(IslandConfig $config) : Island{
		$island = new Island(
			$config->id,
			$config->type,
			new IslandBounds($config->minBounds->toVector3(), $config->maxBounds->toVector3())
		);

		foreach($config->spawnPoints as $spawnData){
			$island->addSpawnPoint(new IslandSpawnPoint(
				$spawnData['position']->toVector3(),
				$spawnData['yaw'] ?? 0.0,
				$spawnData['pitch'] ?? 0.0
			));
		}

		foreach($config->chestPositions as $chestData){
			$island->addChest(new IslandChest(
				$chestData['position']->toVector3(),
				$chestData['isLarge'] ?? false
			));
		}

		foreach($config->generatorPositions as $generatorPosition){
			$island->addGeneratorPosition($generatorPosition->toVector3());
		}

		return $island;
	}

	public function createDefaultMidIsland() : Island{
		$config = new IslandConfig(
			'mid',
			IslandType::MID,
			new SerializableVector3(0, 60, 0),
			new SerializableVector3(10, 70, 10),
			[
				['position' => new SerializableVector3(5, 65, 5), 'yaw' => 0.0, 'pitch' => 0.0]
			],
			[
				['position' => new SerializableVector3(3, 64, 3), 'isLarge' => true],
				['position' => new SerializableVector3(7, 64, 7), 'isLarge' => true]
			]
		);

		return $this->createFromConfig($config);
	}
}
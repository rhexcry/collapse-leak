<?php

declare(strict_types=1);

namespace collapse\game\duel\modes\skywars\island\config;

use collapse\game\duel\modes\skywars\island\IslandBounds;
use collapse\game\duel\modes\skywars\island\IslandType;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use pocketmine\world\Position;
use function file_exists;
use function var_dump;


final readonly class IslandConfigLoader{

	public function loadFromJson(string $configPath) : array{
		if(!file_exists($configPath)){
			throw new \RuntimeException("Island config file not found: $configPath");
		}

		$config = new Config($configPath, Config::JSON);

		$islandConfigs = [];

		foreach($config->get('islands', []) as $islandData){
			var_dump($islandData);
			$islandConfigs[] = $this->createIslandConfigFromArray($islandData);
		}

		return $islandConfigs;
	}

	private function createIslandConfigFromArray(array $data) : IslandConfig{
		return new IslandConfig(
			$data['id'],
			new IslandBounds(
				new Vector3($data['bounds']['minX'], $data['bounds']['minY'], $data['bounds']['minZ']),
				new Vector3($data['bounds']['maxX'], $data['bounds']['maxY'], $data['bounds']['maxZ'])),
			IslandType::from($data['type']),
			array_map(function(array $d) : Position{
				return new Position($d['x'], $d['y'], $d['z'], null);
			}, $data['chestPositions'])
		);
	}
}
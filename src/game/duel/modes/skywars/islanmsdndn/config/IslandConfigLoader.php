<?php

declare(strict_types=1);

namespace collapse\game\duel\modes\skywars\islanmsdndn\config;

use collapse\utils\SerializableVector3;
use collapse\game\duel\modes\skywars\islanmsdndn\IslandType;

class IslandConfigLoader{

	public function loadFromJson(string $configPath) : array{
		if(!file_exists($configPath)){
			throw new \RuntimeException("Island config file not found: $configPath");
		}

		$jsonContent = file_get_contents($configPath);
		if($jsonContent === false){
			throw new \RuntimeException("Failed to read config file: $configPath");
		}

		$config = json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR);

		if(!is_array($config) || !isset($config['islands'])){
			throw new \RuntimeException("Invalid island config format");
		}

		$islandConfigs = [];

		foreach($config['islands'] as $islandData){
			$islandConfigs[] = $this->createIslandConfigFromArray($islandData);
		}

		return $islandConfigs;
	}

	public function saveToJson(array $islandConfigs, string $configPath) : void{
		$config = [
			'version' => '1.0.0',
			'created_at' => date('c'),
			'islands' => []
		];

		foreach($islandConfigs as $islandConfig){
			$config['islands'][] = $this->islandConfigToArray($islandConfig);
		}

		$json = json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
		file_put_contents($configPath, $json, LOCK_EX);
	}

	private function createIslandConfigFromArray(array $data) : IslandConfig{
		return new IslandConfig(
			$this->getString($data, 'id'),
			IslandType::from($this->getString($data, 'type', 'start')),
			SerializableVector3::fromArray($data['min_bounds'] ?? []),
			SerializableVector3::fromArray($data['max_bounds'] ?? []),
			$this->parseSpawnPoints($data['spawn_points'] ?? []),
			$this->parseChests($data['chests'] ?? []),
			$this->parseGeneratorPositions($data['generators'] ?? [])
		);
	}

	private function islandConfigToArray(IslandConfig $config) : array{
		return [
			'id' => $config->id,
			'type' => $config->type->value,
			'min_bounds' => $config->minBounds->toArray(),
			'max_bounds' => $config->maxBounds->toArray(),
			'spawn_points' => $this->spawnPointsToArray($config->spawnPoints),
			'chests' => $this->chestsToArray($config->chestPositions),
			'generators' => $this->generatorsToArray($config->generatorPositions),
			'description' => $this->generateDescription($config)
		];
	}

	private function parseSpawnPoints(array $spawnPointsData) : array{
		$spawnPoints = [];

		foreach($spawnPointsData as $spawnData){
			$spawnPoints[] = [
				'position' => SerializableVector3::fromArray($spawnData['position'] ?? []),
				'yaw' => (float) ($spawnData['yaw'] ?? 0.0),
				'pitch' => (float) ($spawnData['pitch'] ?? 0.0)
			];
		}

		return $spawnPoints;
	}

	private function spawnPointsToArray(array $spawnPoints) : array{
		$result = [];

		foreach($spawnPoints as $spawnPoint){
			$result[] = [
				'position' => $spawnPoint['position']->toArray(),
				'yaw' => $spawnPoint['yaw'],
				'pitch' => $spawnPoint['pitch']
			];
		}

		return $result;
	}

	private function parseChests(array $chestsData) : array{
		$chests = [];

		foreach($chestsData as $chestData){
			$chests[] = [
				'position' => SerializableVector3::fromArray($chestData['position'] ?? []),
				'isLarge' => (bool) ($chestData['is_large'] ?? $chestData['isLarge'] ?? false)
			];
		}

		return $chests;
	}

	private function chestsToArray(array $chests) : array{
		$result = [];

		foreach($chests as $chest){
			$result[] = [
				'position' => $chest['position']->toArray(),
				'is_large' => $chest['isLarge']
			];
		}

		return $result;
	}

	private function parseGeneratorPositions(array $generatorsData) : array{
		$generators = [];

		foreach($generatorsData as $generatorData){
			$generators[] = SerializableVector3::fromArray($generatorData);
		}

		return $generators;
	}

	private function generatorsToArray(array $generators) : array{
		$result = [];

		foreach($generators as $generator){
			$result[] = $generator->toArray();
		}

		return $result;
	}

	private function generateDescription(IslandConfig $config) : string{
		return sprintf(
			"%s island with %d spawn points, %d chests, and %d generators",
			$config->type->getDisplayName(),
			count($config->spawnPoints),
			count($config->chestPositions),
			count($config->generatorPositions)
		);
	}

	private function getString(array $data, string $key, string $default = '') : string{
		return isset($data[$key]) ? (string) $data[$key] : $default;
	}

	public function validateConfig(array $config) : bool{
		if(!isset($config['islands']) || !is_array($config['islands'])){
			return false;
		}

		foreach($config['islands'] as $islandData){
			if(!$this->validateIslandData($islandData)){
				return false;
			}
		}

		return true;
	}

	private function validateIslandData(array $data) : bool{
		$required = ['id', 'type', 'min_bounds', 'max_bounds'];

		foreach($required as $key){
			if(!isset($data[$key])){
				return false;
			}
		}

		return true;
	}
}
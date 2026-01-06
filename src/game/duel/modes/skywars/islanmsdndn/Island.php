<?php

declare(strict_types=1);

namespace collapse\game\duel\modes\skywars\islanmsdndn;

use collapse\game\teams\Team;
use pocketmine\math\Vector3;

final class Island{

	/** @var IslandSpawnPoint[] */
	private array $spawnPoints = [];

	/** @var IslandChest[] */
	private array $chests = [];

	/** @var Vector3[] */
	private array $generatorPositions = [];

	public function __construct(
		private string       $id,
		private IslandType   $type,
		private IslandBounds $bounds,
		private ?Team        $team = null
	){
	}

	public function getId() : string{
		return $this->id;
	}

	public function getType() : IslandType{
		return $this->type;
	}

	public function getBounds() : IslandBounds{
		return $this->bounds;
	}

	public function getTeam() : ?Team{
		return $this->team;
	}

	public function setTeam(?Team $team) : void{
		$this->team = $team;
	}

	public function isMidIsland() : bool{
		return $this->type === IslandType::MID;
	}

	public function isStartIsland() : bool{
		return $this->type === IslandType::START;
	}

	public function addSpawnPoint(IslandSpawnPoint $spawnPoint) : void{
		$this->spawnPoints[] = $spawnPoint;
	}

	/**
	 * @return IslandSpawnPoint[]
	 */
	public function getSpawnPoints() : array{
		return $this->spawnPoints;
	}

	public function getRandomSpawnPoint() : ?IslandSpawnPoint{
		if(empty($this->spawnPoints)){
			return null;
		}

		return $this->spawnPoints[array_rand($this->spawnPoints)];
	}

	public function addChest(IslandChest $chest) : void{
		$this->chests[] = $chest;
	}

	/**
	 * @return IslandChest[]
	 */
	public function getChests() : array{
		return $this->chests;
	}

	/**
	 * @return Vector3[]
	 */
	public function getChestPositions() : array{
		return array_map(fn(IslandChest $chest) => $chest->getPosition(), $this->chests);
	}

	public function addGeneratorPosition(Vector3 $position) : void{
		$this->generatorPositions[] = $position;
	}

	/**
	 * @return Vector3[]
	 */
	public function getGeneratorPositions() : array{
		return $this->generatorPositions;
	}

	public function getCenter() : Vector3{
		return $this->bounds->getCenter();
	}

	public function distanceTo(Island $otherIsland) : float{
		return $this->getCenter()->distance($otherIsland->getCenter());
	}
}
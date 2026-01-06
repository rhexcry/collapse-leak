<?php

declare(strict_types=1);

namespace collapse\game\duel\modes\skywars\islanmsdndn;

use collapse\game\teams\Team;
use pocketmine\world\Position;

final class IslandManager{

	/** @var Island[] */
	private array $islands = [];

	/** @var array<string, Island[]> */
	private array $islandsByType = [];

	public function addIsland(Island $island) : void{
		$this->islands[$island->getId()] = $island;
		$this->islandsByType[$island->getType()->value][] = $island;
	}

	public function getIsland(string $id) : ?Island{
		return $this->islands[$id] ?? null;
	}

	/**
	 * @return Island[]
	 */
	public function getIslands() : array{
		return $this->islands;
	}

	/**
	 * @return Island[]
	 */
	public function getIslandsByType(IslandType $type) : array{
		return $this->islandsByType[$type->value] ?? [];
	}

	/**
	 * @return Island[]
	 */
	public function getStartIslands() : array{
		return $this->getIslandsByType(IslandType::START);
	}

	public function getMidIsland() : ?Island{
		$midIslands = $this->getIslandsByType(IslandType::MID);
		return $midIslands[0] ?? null;
	}

	public function assignTeamsToStartIslands(array $teams) : void{
		$startIslands = $this->getStartIslands();

		if(count($teams) > count($startIslands)){
			throw new \InvalidArgumentException('Not enough start islands for teams');
		}

		shuffle($startIslands);

		foreach($teams as $index => $team){
			if(isset($startIslands[$index])){
				$startIslands[$index]->setTeam($team);
			}
		}
	}

	public function getIslandForTeam(Team $team) : ?Island{
		foreach($this->getStartIslands() as $island){
			if($island->getTeam()?->getId() === $team->getId()){
				return $island;
			}
		}

		return null;
	}

	public function getRandomSpawnPointForTeam(Team $team) : ?IslandSpawnPoint{
		$island = $this->getIslandForTeam($team);
		return $island?->getRandomSpawnPoint();
	}

	public function getIslandAtPosition(Position $position) : ?Island{
		foreach($this->islands as $island){
			if($island->getBounds()->contains($position)){
				return $island;
			}
		}

		return null;
	}
}
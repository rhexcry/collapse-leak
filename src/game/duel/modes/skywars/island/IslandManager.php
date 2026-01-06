<?php

declare(strict_types=1);

namespace collapse\game\duel\modes\skywars\island;

use function count;
use function shuffle;
use function var_dump;

final class IslandManager{

	private array $islandsIdToMap = [];

	private array $islandsTypeToMap = [];

	public function addIsland(Island $island) : void{
		$this->islandsIdToMap[$island->getId()] = $island;
		$this->islandsTypeToMap[$island->getType()->value] = $island;
	}

	public function getIslandsByType(IslandType $type) : array{
		$islands = [];
		var_dump($this->islandsTypeToMap);
		foreach($this->islandsTypeToMap as $islandType => $island){
			if($islandType === $type->value){
				$islands[] = $island;
			}
		}

		return $islands;
	}

	public function assignTeamsToStartIslands(array $teams) : void{
		$startIslands = $this->getIslandsByType(IslandType::START);
		var_dump($startIslands);

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
}
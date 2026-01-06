<?php

declare(strict_types=1);

namespace collapse\game\duel;

use collapse\game\teams\Team;
use collapse\player\CollapsePlayer;
use pocketmine\entity\Location;
use function array_shift;
use function count;

final class DuelSpawnManager{

	/** @var Location[] */
	private array $spawns = [];

	/** @var Location[] */
	private array $entries = [];

	public function __construct(
		private readonly Duel $duel
	){
		$world = $this->duel->getWorldManager()->getWorld();
		foreach($this->duel->getConfig()->getSpawnLocations() as $spawn){
			$spawn = clone $spawn;
			$spawn->world = $world;
			$this->spawns[] = $spawn;
		}

		$this->checkSpawnsCount();

		foreach($this->duel->getTeamManager()->getTeams() as $team){
			$this->setSpawn($team, array_shift($this->spawns));
		}
	}

	private function checkSpawnsCount() : void{
		if(count($this->spawns) !== count($this->duel->getTeamManager()->getTeams())){
			throw new \InvalidArgumentException('Mismatch spawns count ' . count($this->spawns) . ' for ' . count($this->duel->getTeamManager()->getTeams()) . ' teams');
		}
	}

	public function setSpawn(Team|CollapsePlayer $owner, Location $spawn) : void{
		$this->entries[$owner instanceof Team ? $owner->getId() : $owner->getName()] = $spawn;
	}

	public function getSpawn(Team|CollapsePlayer $owner) : ?Location{
		return $this->entries[$owner instanceof Team ? $owner->getId() : $owner->getName()] ?? null;
	}
}

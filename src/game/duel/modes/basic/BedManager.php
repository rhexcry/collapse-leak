<?php

declare(strict_types=1);

namespace collapse\game\duel\modes\basic;

use collapse\game\duel\Duel;
use collapse\game\duel\modes\basic\event\BedDestroyEvent;
use collapse\game\teams\Team;
use collapse\game\teams\TeamUtils;
use collapse\player\CollapsePlayer;
use pocketmine\block\Bed;

final class BedManager{

	/** @var bool[] */
	private array $beds = [];

	public function __construct(private readonly Duel $duel){
		foreach($this->duel->getTeamManager()->getTeams() as $team){
			$this->beds[$team->getId()] = true;
		}
	}

	public function isBedAlive(Team $team) : bool{
		return isset($this->beds[$team->getId()]) && $this->beds[$team->getId()];
	}

	public function onBedDestroy(CollapsePlayer $player, Bed $bed) : bool{
		$id = TeamUtils::bedToTeamId($bed->getColor());
		if($id === null){
			return false;
		}
		$team = $this->duel->getTeamManager()->getTeam($id);
		if($team === null){
			return false;
		}
		if(!isset($this->beds[$team->getId()])){
			return false;
		}

		$ev = new BedDestroyEvent($team, $player);
		$ev->call();
		if($ev->isCancelled()){
			return false;
		}
		$this->beds[$team->getId()] = false;
		$this->onForceScoreboardUpdate();
		return true;
	}

	public function onForceScoreboardUpdate() : void{
		foreach($this->duel->getPlayerManager()->getPlayers() as $player){
			$scoreboard = $player->getScoreboard();
			if($scoreboard instanceof BedsScoreboard){
				$scoreboard->onForceUpdate();
			}
		}
	}
}

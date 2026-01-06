<?php

declare(strict_types=1);

namespace collapse\game\duel\modes\skywars;

use collapse\game\duel\Duel;
use collapse\game\duel\modes\basic\TeamScoreboard;
use collapse\game\teams\Team;

final class SkyWarsTeamManager{

	/** @var Team[] */
	private array $teams = [];

	public function __construct(private readonly Duel $duel){
		foreach($this->duel->getTeamManager()->getTeams() as $team){
			$this->teams[$team->getId()] = true;
		}
	}

	public function isTeamAlive(Team $team) : bool{
		return isset($this->teams[$team->getId()]);
	}

	public function onForceScoreboardUpdate() : void{
		foreach($this->duel->getPlayerManager()->getPlayers() as $player){
			$scoreboard = $player->getScoreboard();
			if($scoreboard instanceof TeamScoreboard){
				$scoreboard->onForceUpdate();
			}
		}
	}
}
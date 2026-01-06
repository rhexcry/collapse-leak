<?php

declare(strict_types=1);

namespace collapse\game\teams;

use collapse\game\Game;
use function array_filter;
use function count;

final class TeamManager{

	/** @var Team[] */
	private array $teams;

	private ?Game $game = null;

	public function __construct(
		private readonly int $playersPerTeam,
		private readonly int $teamsCount
	){
		$this->teams = Teams::create($this->teamsCount);
	}

	public function setGame(Game $game) : void{
		$this->game = $game;
		foreach($this->teams as $team){
			$team->setGame($game);
		}
	}

	public function getGame() : ?Game{
		return $this->game;
	}

	public function getTeam(string $id) : ?Team{
		return $this->teams[$id] ?? null;
	}

	public function getPlayersPerTeam() : int{
		return $this->playersPerTeam;
	}

	public function getTeamsCount() : int{
		return $this->teamsCount;
	}

	public function getTeams() : array{
		return $this->teams;
	}

	public function getFreeTeam() : ?Team{
		foreach($this->teams as $team){
			if(count($team->getPlayers()) < $this->playersPerTeam){
				return $team;
			}
		}
		return null;
	}

	/**
	 * @return Team[]
	 */
	public function getAliveTeams() : array{
		return array_filter($this->teams, fn(Team $team) : bool => count($team->getPlayers()) > 0);
	}
}

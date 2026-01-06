<?php

declare(strict_types=1);

namespace collapse\game\duel;

use collapse\game\teams\Team;
use collapse\player\CollapsePlayer;

final class DuelOpponentManager{

	/** @var (CollapsePlayer|Team)[] */
	private array $opponents = [];

	public function __construct(
		private readonly Duel $duel
	){}

	public function onAllPlayersJoined() : void{
		if($this->duel->getType()->isSolo()){
			foreach($this->duel->getPlayerManager()->getPlayers() as $player){
				foreach($this->duel->getPlayerManager()->getPlayers() as $otherPlayer){
					if($otherPlayer === $player){
						continue;
					}
					$this->opponents[$player->getName()] = $otherPlayer;
				}
			}
		}else{
			foreach($this->duel->getTeamManager()->getTeams() as $team){
				foreach($this->duel->getTeamManager()->getTeams() as $otherTeam){
					if($otherTeam === $team){
						continue;
					}
					$this->opponents[$team->getId()] = $otherTeam;
				}
			}
		}
	}

	public function getOpponent(CollapsePlayer|Team $target) : CollapsePlayer|Team|null{
		return $this->opponents[$target instanceof CollapsePlayer ? $target->getName() : $target->getId()] ?? null;
	}

	public function setOpponent(CollapsePlayer|Team $owner, CollapsePlayer|Team|null $target) : void{
		$this->opponents[$owner instanceof CollapsePlayer ? $owner->getName() : $owner->getId()] = $target;
	}

	public function getOpponents() : array{
		return $this->opponents;
	}

	public function removeOpponent(CollapsePlayer|Team $target) : void{
		unset($this->opponents[$target instanceof CollapsePlayer ? $target->getName() : $target->getId()]);
	}
}

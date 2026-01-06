<?php

declare(strict_types=1);

namespace collapse\game\duel\phase;

use collapse\game\duel\Duel;
use collapse\game\duel\phase\running\PhaseRunning;
use function time;

final class DuelPhaseManager{

	private Phase $phase;

	private PhaseRunning $phaseRunning;

	private ?int $lastUpdate = null;

	public function __construct(
		private readonly Duel $duel
	){}

	public function setPhase(Phase $phase) : void{
		$this->phase = $phase;
		$this->phase->onSet();
		foreach($this->duel->getPlayerManager()->getPlayers() as $player){
			$this->phase->setScoreboard($player);
		}
	}

	public function getPhase() : Phase{
		return $this->phase;
	}

	public function setBasePhaseRunning(PhaseRunning $phase) : void{
		$this->phaseRunning = $phase;
	}

	public function getBasePhaseRunning() : PhaseRunning{
		return $this->phaseRunning;
	}

	public function onUpdate() : void{
		$update = time();
		if($this->lastUpdate !== null && $this->lastUpdate === $update){
			return;
		}
		if($this->duel->getRecord()->isDurationEnabled()){
			$this->duel->getRecord()->addDuration();
		}
		$this->lastUpdate = $update;
		$this->phase->onUpdate();
	}
}

<?php

declare(strict_types=1);

namespace collapse\game\duel\phase\end;

use collapse\game\duel\phase\Phase;
use collapse\player\CollapsePlayer;

class PhaseEnd extends Phase{

	private int $countdown = 5;

	public function setScoreboard(CollapsePlayer $player) : void{
		$player->setScoreboard(new PhaseEndScoreboard($player, $this->duel, $this));
	}

	public function onUpdate() : void{
		if(--$this->countdown <= 0){
			$this->duel->getPlugin()->getDuelManager()->closeDuel($this->duel);
		}
	}
}

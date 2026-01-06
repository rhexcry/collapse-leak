<?php

declare(strict_types=1);

namespace collapse\game\duel\tasks;

use collapse\game\duel\DuelManager;
use pocketmine\scheduler\Task;

final class DuelsUpdateTask extends Task{

	public function __construct(
		private readonly DuelManager $duelManager
	){}

	public function onRun() : void{
		foreach($this->duelManager->getDuels() as $duel){
			$duel->getPhaseManager()->onUpdate();
		}
	}
}

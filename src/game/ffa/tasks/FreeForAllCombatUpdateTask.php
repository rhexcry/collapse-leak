<?php

declare(strict_types=1);

namespace collapse\game\ffa\tasks;

use collapse\game\ffa\FreeForAllManager;
use pocketmine\scheduler\Task;

final class FreeForAllCombatUpdateTask extends Task{

	public function __construct(
		private readonly FreeForAllManager $freeForAllManager
	){}

	public function onRun() : void{
		foreach($this->freeForAllManager->getArenas() as $arena){
			if(count($arena->getPlayerManager()->getPlayers()) === 0){
				continue;
			}
			$arena->getOpponentManager()?->update();
		}
	}
}

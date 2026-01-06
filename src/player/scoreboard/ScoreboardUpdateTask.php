<?php

declare(strict_types=1);

namespace collapse\player\scoreboard;

use collapse\Practice;
use pocketmine\scheduler\Task;

class ScoreboardUpdateTask extends Task{

	public function onRun() : void{
		foreach(Practice::onlinePlayers() as $player){
			if(!$player->isConnected()){
				continue;
			}
			$scoreboard = $player->getScoreboard();
			$scoreboard?->onUpdate();
			$scoreboard?->flushUpdates();
		}
	}
}

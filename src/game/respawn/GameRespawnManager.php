<?php

declare(strict_types=1);

namespace collapse\game\respawn;

use collapse\game\Game;
use collapse\player\CollapsePlayer;

class GameRespawnManager{

	/** @var PlayerRespawnTask[] */
	private array $respawns = [];

	public function __construct(
		private readonly Game $game,
		private readonly int $countdown
	){}

	public function getGame() : Game{
		return $this->game;
	}

	public function hasRespawnTask(CollapsePlayer $player) : bool{
		return isset($this->respawns[$player->getName()]);
	}

	public function respawn(CollapsePlayer $player, ?int $countdown = null) : void{
		if(isset($this->respawns[$player->getName()])){
			return;
		}
		$this->respawns[$player->getName()] = $task = new PlayerRespawnTask($player, $this, $countdown ?? $this->countdown);
		$this->game->getPlugin()->getScheduler()->scheduleRepeatingTask($task, 20);
		(new PlayerStartRespawnEvent($player))->call();
	}

	public function onPlayerRespawn(CollapsePlayer $player) : void{
		if(isset($this->respawns[$player->getName()])){
			(new PlayerRespawnGameEvent($player, $this->game))->call();
			$this->respawns[$player->getName()]->getHandler()?->cancel();
			unset($this->respawns[$player->getName()]);
		}
	}

	public function cancelRespawn(CollapsePlayer $player) : void{
		if(isset($this->respawns[$player->getName()])){
			$this->respawns[$player->getName()]->getHandler()?->cancel();
			unset($this->respawns[$player->getName()]);
		}
	}
}

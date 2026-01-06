<?php

declare(strict_types=1);

namespace collapse\game\event;

use collapse\game\Game;
use collapse\player\CollapsePlayer;
use pocketmine\lang\Translatable;

final class PlayerKillPlayerGameEvent extends GameEvent{

	public function __construct(
		Game $game,
		private readonly CollapsePlayer $player,
		private CollapsePlayer $killer,
		private readonly int $cause,
		private ?Translatable $broadcastMessage = null,
	){
		parent::__construct($game);
	}

	public function getPlayer() : CollapsePlayer{
		return $this->player;
	}

	public function setKiller(CollapsePlayer $killer) : void{
		$this->killer = $killer;
	}

	public function getKiller() : CollapsePlayer{
		return $this->killer;
	}

	public function getCause() : int{
		return $this->cause;
	}

	public function setBroadcastMessage(?Translatable $broadcastMessage) : void{
		$this->broadcastMessage = $broadcastMessage;
	}

	public function getBroadcastMessage() : ?Translatable{
		return $this->broadcastMessage;
	}
}

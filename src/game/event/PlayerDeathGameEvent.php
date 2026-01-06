<?php

declare(strict_types=1);

namespace collapse\game\event;

use collapse\game\Game;
use collapse\player\CollapsePlayer;
use pocketmine\lang\Translatable;

final class PlayerDeathGameEvent extends GameEvent{

	public function __construct(
		Game $game,
		private readonly CollapsePlayer $player,
		private readonly int $cause,
		private ?Translatable $deathMessage = null,
	){
		parent::__construct($game);
	}

	public function getPlayer() : CollapsePlayer{
		return $this->player;
	}

	public function getCause() : int{
		return $this->cause;
	}

	public function setDeathMessage(?Translatable $deathMessage) : void{
		$this->deathMessage = $deathMessage;
	}

	public function getDeathMessage() : ?Translatable{
		return $this->deathMessage;
	}
}

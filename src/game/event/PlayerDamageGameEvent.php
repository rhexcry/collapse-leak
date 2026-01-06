<?php

declare(strict_types=1);

namespace collapse\game\event;

use collapse\game\Game;
use collapse\player\CollapsePlayer;
use pocketmine\event\entity\EntityDamageEvent;

final class PlayerDamageGameEvent extends GameEvent{

	public function __construct(
		Game $game,
		private readonly CollapsePlayer $player,
		private readonly EntityDamageEvent $subEvent
	){
		parent::__construct($game);
	}

	public function getPlayer() : CollapsePlayer{
		return $this->player;
	}

	public function getSubEvent() : EntityDamageEvent{
		return $this->subEvent;
	}
}

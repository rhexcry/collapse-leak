<?php

declare(strict_types=1);

namespace collapse\game\event;

use collapse\game\Game;
use collapse\player\CollapsePlayer;
use pocketmine\event\entity\EntityDamageByEntityEvent;

final class PlayerAttackPlayerGameEvent extends GameEvent{

	public function __construct(
		Game $game,
		private readonly CollapsePlayer $player,
		private readonly CollapsePlayer $attacker,
		private readonly EntityDamageByEntityEvent $subEvent
	){
		parent::__construct($game);
	}

	public function getPlayer() : CollapsePlayer{
		return $this->player;
	}

	public function getAttacker() : CollapsePlayer{
		return $this->attacker;
	}

	public function getSubEvent() : EntityDamageByEntityEvent{
		return $this->subEvent;
	}
}

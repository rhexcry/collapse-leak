<?php

declare(strict_types=1);

namespace collapse\game\event;

use collapse\game\Game;
use collapse\player\CollapsePlayer;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\CancellableTrait;

final class BlockPlaceGameEvent extends GameEvent{
	use CancellableTrait;

	public function __construct(
		Game $game,
		private readonly CollapsePlayer $player,
		private readonly BlockPlaceEvent $subEvent
	){
		parent::__construct($game);
	}

	public function getPlayer() : CollapsePlayer{
		return $this->player;
	}

	public function getSubEvent() : BlockPlaceEvent{
		return $this->subEvent;
	}
}

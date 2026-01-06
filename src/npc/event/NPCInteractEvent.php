<?php

declare(strict_types=1);

namespace collapse\npc\event;

use collapse\npc\CollapseNPC;
use collapse\player\CollapsePlayer;
use pocketmine\event\CancellableTrait;
use pocketmine\event\entity\EntityEvent;

final class NPCInteractEvent extends EntityEvent{
	use CancellableTrait;

	public function __construct(private readonly CollapsePlayer $player, private readonly CollapseNPC $npc){
	}

	public function getPlayer() : CollapsePlayer{
		return $this->player;
	}

	public function getNPC() : CollapseNPC{
		return $this->npc;
	}
}

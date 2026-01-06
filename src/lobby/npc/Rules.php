<?php

declare(strict_types=1);

namespace collapse\lobby\npc;

use collapse\player\CollapsePlayer;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;

final class Rules extends LobbyNPC{

	public function __construct(Location $location, Skin $skin){
		parent::__construct($location, $skin);
		$this->setScale(0.5);
	}

	protected function handlePlayerInteract(CollapsePlayer $player) : void{

	}
}

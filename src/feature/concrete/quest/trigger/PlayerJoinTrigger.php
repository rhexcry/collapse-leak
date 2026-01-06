<?php

declare(strict_types=1);

namespace collapse\feature\concrete\quest\trigger;

use collapse\feature\trigger\types\EventTrigger;
use pocketmine\event\player\PlayerJoinEvent;
use function in_array;

final class PlayerJoinTrigger extends EventTrigger{

	public function shouldHandle(object $event) : bool{
		return in_array($event::class, $this->getHandleableEvents(), true);
	}

	public function getHandleableEvents() : array{
		return [PlayerJoinEvent::class];
	}

	public function execute(object $event) : void{
		$shouldExecute = false;
		$player = $event->getPlayer();
		foreach($this->getConditions() as $condition){
			if($condition->isMet($player, []) && !$shouldExecute){
				$shouldExecute = true;
			}
		}

		if($shouldExecute){
			$this->executeActionsFor($player);
		}
	}
}

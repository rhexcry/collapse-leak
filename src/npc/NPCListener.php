<?php

declare(strict_types=1);

namespace collapse\npc;

use collapse\player\CollapsePlayer;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

final readonly class NPCListener implements Listener{

	public function __construct(
		private NPCManager $npcManager
	){}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerJoin(PlayerJoinEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		foreach($this->npcManager->getAll() as $npc){
			$npc->onPlayerJoin($player);
		}
	}
}

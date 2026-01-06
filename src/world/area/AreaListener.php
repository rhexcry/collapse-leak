<?php

declare(strict_types=1);

namespace collapse\world\area;

use collapse\player\CollapsePlayer;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;

final readonly class AreaListener implements Listener{

	public function __construct(
		private AreaManager $areaManager
	){}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerMove(PlayerMoveEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		$this->areaManager->onChunkIntersection($player);
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerQuit(PlayerQuitEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		$player->removeAllCollidedAreas();
	}
}

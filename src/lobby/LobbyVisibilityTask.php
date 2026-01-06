<?php

declare(strict_types=1);

namespace collapse\lobby;

use collapse\player\CollapsePlayer;
use pocketmine\scheduler\Task;
use function array_filter;

final class LobbyVisibilityTask extends Task{

	public function __construct(
		private readonly LobbyManager $lobbyManager
	){}

	public function onRun() : void{
		$spawnLocation = $this->lobbyManager->getSpawnLocation();
		foreach(array_filter($this->lobbyManager->getPlayers(), fn(CollapsePlayer $player) : bool => $this->lobbyManager->isHiddenFromPlayers($player)) as $player){
			if($player->getLocation()->distance($spawnLocation) < 2){
				continue;
			}
			$this->lobbyManager->showToPlayers($player);
		}
	}
}

<?php

declare(strict_types=1);

namespace collapse\lobby;

use collapse\player\CollapsePlayer;
use pocketmine\scheduler\Task;
use function array_filter;

final class LobbyAntiFallTask extends Task{

	public function __construct(
		private readonly LobbyManager $lobbyManager
	){}

	public function onRun() : void{
		$spawnY = $this->lobbyManager->getSpawnLocation()->getY();
		$distance = 20;

		foreach(array_filter($this->lobbyManager->getPlayers(), fn(CollapsePlayer $player) : bool => $player->checkWorldValid() && $player->getPosition()->getY() < $spawnY - $distance) as $player){
			if($player->getGame() !== null){
				continue;
			}
			$player->teleport($this->lobbyManager->getSpawnLocation());
		}
	}
}
<?php

declare(strict_types=1);

namespace collapse\game\respawn;

use collapse\player\CollapsePlayer;
use pocketmine\scheduler\Task;

final class PlayerRespawnTask extends Task{

	public function __construct(
		private readonly ?CollapsePlayer $player,
		private readonly GameRespawnManager $respawnManager,
		private int $countdown
	){}

	public function onRun() : void{
		if($this->player === null || !$this->player->isConnected() || $this->player->getGame() !== $this->respawnManager->getGame()){
			$this->getHandler()?->cancel();
			return;
		}

		if($this->countdown <= 0){
			$this->respawnManager->onPlayerRespawn($this->player);
			return;
		}

		//$this->player->sendTranslatedTitle(CollapseTranslationFactory::free_for_all_respawn_after((string) $this->countdown));

		(new PlayerRespawnUpdateEvent($this->player, $this->countdown--))->call();
	}
}

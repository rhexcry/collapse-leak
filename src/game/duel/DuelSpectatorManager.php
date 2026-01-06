<?php

declare(strict_types=1);

namespace collapse\game\duel;

use collapse\game\duel\item\DuelItems;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use pocketmine\entity\Location;
use pocketmine\lang\Translatable;
use pocketmine\player\GameMode;

final class DuelSpectatorManager{

	/** @var CollapsePlayer[] */
	private array $spectators = [];

	public function __construct(
		private readonly Duel $duel
	){}

	public function getSpectators() : array{
		return $this->spectators;
	}

	public function hasSpectator(CollapsePlayer $player) : bool{
		return isset($this->spectators[$player->getName()]);
	}

	public function addSpectator(CollapsePlayer $player, CollapsePlayer $target) : void{
		if(!$target->isConnected() || $target->getWorld() !== $this->duel->getWorldManager()->getWorld()){
			$this->duel->getPlugin()->getLobbyManager()->sendToLobby($player);
			return;
		}

		$this->spectators[$player->getName()] = $player;
		$player->setSpectatingGame($this->duel);
		$player->setBasicProperties(GameMode::SPECTATOR);
		$player->teleport(Location::fromObject($target->getLocation()->add(0, 3, 0), $target->getWorld()));
		$player->getInventory()->setItem(8, DuelItems::STOP_SPECTATING_MATCH()->translate($player));
	}

	public function removeSpectator(CollapsePlayer $player) : void{
		unset($this->spectators[$player->getName()]);
		$player->setSpectatingGame(null);
		$player->setBasicProperties($player->getGamemode());
		$lobbyManager = $this->duel->getPlugin()->getLobbyManager();
		$lobbyManager->sendToLobby($player);
	}

	public function onStoppedSpectating(CollapsePlayer $player) : void{
		$this->duel->getPlayerManager()->broadcastMessage(CollapseTranslationFactory::duels_stopped_spectating($player->getNameWithRankColor()));
		$this->broadcastMessage(CollapseTranslationFactory::duels_stopped_spectating($player->getNameWithRankColor()));
		$this->removeSpectator($player);
	}

	public function broadcastMessage(Translatable $translation, bool $prefix = true) : void{
		foreach($this->spectators as $player){
			if($player->isConnected()){
				$player->sendTranslatedMessage($translation, $prefix);
			}
		}
	}

	public function close() : void{
		$lobbyManager = $this->duel->getPlugin()->getLobbyManager();
		foreach($this->spectators as $player){
			if($player->isConnected()){
				$this->removeSpectator($player);
				$lobbyManager->sendToLobby($player);
			}
		}
	}
}

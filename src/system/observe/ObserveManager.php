<?php

declare(strict_types=1);

namespace collapse\system\observe;

use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\system\observe\command\ObserveCommand;
use collapse\system\observe\event\ObserveStartEvent;
use collapse\system\observe\event\ObserveStopEvent;
use collapse\system\observe\item\ObserveItems;
use collapse\system\observe\scoreboard\ObserveScoreboard;
use pocketmine\player\GameMode;
use function array_filter;

class ObserveManager{

	/** @var ObserverSession[] */
	private array $sessions = [];

	public function __construct(
		private readonly Practice $plugin
	){
		ObserveItems::init();
		$this->plugin->getServer()->getCommandMap()->register('collapse', new ObserveCommand($this));
		$this->plugin->getServer()->getPluginManager()->registerEvents(new ObserveListener(), $this->plugin);
	}

	public function startObserving(CollapsePlayer $observer, CollapsePlayer $target) : void{
		if(($game = $observer->getGame()) !== null){
			$game->onPlayerLeave($observer);
			$observer->setGame(null);
		}
		$this->plugin->getLobbyManager()->removeFromLobby($observer);
		$this->sessions[$observer->getName()] = $session = new ObserverSession($observer, $target);
		$observer->setBasicProperties(GameMode::SPECTATOR);
		$inventory = $observer->getInventory();
		$inventory->setItem(0, ObserveItems::TELEPORT()->translate($observer));
		$inventory->setItem(2, ObserveItems::ARMOR_TAKE_OFF()->translate($observer));
		$inventory->setItem(8, ObserveItems::STOP_OBSERVE()->translate($observer));
		$session->update();
		$observer->setScoreboard(new ObserveScoreboard($observer, $this));
		(new ObserveStartEvent($observer, $target))->call();
	}

	public function stopObserving(CollapsePlayer $observer) : void{
		if(!isset($this->sessions[$observer->getName()])){
			return;
		}
		$session = $this->sessions[$observer->getName()];
		unset($this->sessions[$observer->getName()]);
		$this->plugin->getLobbyManager()->sendToLobby($observer);
		(new ObserveStopEvent($observer, $session->getTarget()))->call();
	}

	public function isObserving(CollapsePlayer $player) : bool{
		return isset($this->sessions[$player->getName()]);
	}

	public function getSession(CollapsePlayer $player) : ?ObserverSession{
		return $this->sessions[$player->getName()] ?? null;
	}

	/**
	 * @return ObserverSession[]
	 */
	public function getSessionsByTarget(CollapsePlayer $target) : array{
		return array_filter($this->sessions, static fn(ObserverSession $session) : bool => $session->getTarget() === $target);
	}
}
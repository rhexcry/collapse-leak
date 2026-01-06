<?php

declare(strict_types=1);

namespace collapse\system\kiteditor;

use collapse\game\duel\queue\event\PlayerJoinQueueEvent;
use collapse\game\event\PlayerJoinGameEvent;
use collapse\npc\event\NPCInteractEvent;
use collapse\player\CollapsePlayer;
use collapse\system\kiteditor\command\KitCommand;
use pocketmine\event\Listener;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;

final readonly class KitEditorListener implements Listener{

	public function __construct(
		private KitEditorManager $kitEditorManager
	){}

	/**
	 * @priority MONITOR
	 */
	public function handlePlayerQuit(PlayerQuitEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();

		if($this->kitEditorManager->isEditing($player)){
			$this->kitEditorManager->stopEditing($player);
		}
	}

	public function handlePlayerUseItem(PlayerItemUseEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();

		if($this->kitEditorManager->isEditing($player)){
			$event->cancel();
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handleNPCInteract(NPCInteractEvent $event) : void{
		$player = $event->getPlayer();

		if($this->kitEditorManager->isEditing($player)){
			$event->cancel();
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerJoinGame(PlayerJoinGameEvent $event) : void{
		$player = $event->getPlayer();

		if($this->kitEditorManager->isEditing($player)){
			$event->cancel();
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerJoinQueue(PlayerJoinQueueEvent $event) : void{
		$player = $event->getPlayer();

		if($this->kitEditorManager->isEditing($player)){
			$event->cancel();
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handleCommand(CommandEvent $event) : void{
		$sender = $event->getSender();
		$command = $sender->getServer()->getCommandMap()->getCommand(explode(' ', $event->getCommand())[0]);
		if($command === null){
			return;
		}

		if(
			$sender instanceof CollapsePlayer &&
			$this->kitEditorManager->isEditing($sender) &&
			!($command instanceof KitCommand)
		){
			$event->cancel();
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handleInventoryTransaction(InventoryTransactionEvent $event) : void{
		$transaction = $event->getTransaction();
		$source = $transaction->getSource();
		if(!($source instanceof CollapsePlayer)){
			return;
		}

		if(!$this->kitEditorManager->isEditing($source)){
			return;
		}
		$session = $this->kitEditorManager->getSession($source);
		if($session === null){
			return;
		}

		foreach($transaction->getActions() as $action){
			if(
				$action instanceof SlotChangeAction &&
				($action->getInventory() === $source->getOffHandInventory() || $action->getInventory() === $source->getArmorInventory())
			){
				$event->cancel();
			}
		}
	}
}
<?php

declare(strict_types=1);

namespace collapse\item;

use collapse\item\default\CollapseFishingRod;
use collapse\player\CollapsePlayer;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\network\mcpe\protocol\types\InputMode;

final readonly class ItemListener implements Listener{

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerItemHeld(PlayerItemHeldEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		if(!$event->getItem() instanceof CollapseFishingRod && $player->getFishingHook() !== null){
			$player->getFishingHook()->handleHookRetraction();
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerInteract(PlayerInteractEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		$item = $event->getItem();
		if(!$item instanceof InventoryItem || (!$player->isSpectator() && $event->isCancelled())){
			return;
		}
		if($player->getProfile()->getInputMode() === InputMode::TOUCHSCREEN){
			$event->cancel();
			$item->onUse($player);
		}
	}

	/**
	 * @priority LOWEST
	 * @handleCancelled
	 */
	public function handlePlayerUseItem(PlayerItemUseEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		$item = $event->getItem();
		if(!$item instanceof InventoryItem || (!$player->isSpectator() && $event->isCancelled())){
			return;
		}
		$event->cancel();
		$item->onUse($player);
	}

	/**
	 * @priority LOWEST
	 */
	public function handleInventoryTransaction(InventoryTransactionEvent $event) : void{
		$transaction = $event->getTransaction();
		/** @var CollapsePlayer $player */
		$player = $transaction->getSource();
		foreach($transaction->getInventories() as $inventory){
			foreach($transaction->getActions() as $action){
				if(!$action instanceof SlotChangeAction){
					continue;
				}
				$item = $action->getSourceItem();
				if(!$item instanceof WindowInventoryItem){
					continue;
				}
				$item->onClick($player, $inventory);
				$event->cancel();
			}
		}
	}
}

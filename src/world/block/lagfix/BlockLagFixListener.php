<?php

declare(strict_types=1);

namespace collapse\world\block\lagfix;

use collapse\player\CollapsePlayer;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\handler\ItemStackContainerIdTranslator;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemTransactionData;
use pocketmine\network\PacketHandlingException;

final readonly class BlockLagFixListener implements Listener{

	public function handleInventoryTransaction(InventoryTransactionPacket $packet, CollapsePlayer $player) : bool{
		if($packet->trData instanceof UseItemTransactionData && $packet->trData->getActionType() === UseItemTransactionData::ACTION_CLICK_BLOCK){
			$inventoryManager = $player->getNetworkSession()->getInvManager();
			if(!$inventoryManager){
				return false;
			}

			if(count($packet->trData->getActions()) > 50){
				throw new PacketHandlingException("Too many actions in inventory transaction");
			}
			if(count($packet->requestChangedSlots) > 10){
				throw new PacketHandlingException("Too many slot sync requests in inventory transaction");
			}

			$inventoryManager->setCurrentItemStackRequestId($packet->requestId);
			$inventoryManager->addRawPredictedSlotChanges($packet->trData->getActions());

			LagFixSession::get($player)->handleClickBlockTransaction($packet->trData);

			$inventoryManager->syncMismatchedPredictedSlotChanges();

			foreach($packet->requestChangedSlots as $containerInfo){
				foreach($containerInfo->getChangedSlotIndexes() as $netSlot){
					[$windowId, $slot] = ItemStackContainerIdTranslator::translate($containerInfo->getContainerId(), $inventoryManager->getCurrentWindowId(), $netSlot);
					$inventoryAndSlot = $inventoryManager->locateWindowAndSlot($windowId, $slot);
					if($inventoryAndSlot !== null){
						$inventoryManager->onSlotChange($inventoryAndSlot[0], $inventoryAndSlot[1]);
					}
				}
			}

			$inventoryManager->setCurrentItemStackRequestId(null);

			return true;
		}

		return false;
	}

	public function onDataPacketReceive(DataPacketReceiveEvent $event) : void{
		$packet = $event->getPacket();

		if(!($packet instanceof InventoryTransactionPacket)){
			return;
		}

		/** @var CollapsePlayer $player */
		$player = $event->getOrigin()->getPlayer();
		if($player === null){
			return;
		}

		if($this->handleInventoryTransaction($packet, $player)){
			$event->cancel();
		}
	}
}
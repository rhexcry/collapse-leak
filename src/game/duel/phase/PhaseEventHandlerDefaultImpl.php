<?php

declare(strict_types=1);

namespace collapse\game\duel\phase;

use collapse\game\duel\modes\basic\BedsDuel;
use collapse\game\event\BlockBreakGameEvent;
use collapse\game\event\BlockPlaceGameEvent;
use collapse\game\event\PlayerAttackPlayerGameEvent;
use collapse\game\event\PlayerDamageGameEvent;
use collapse\game\event\PlayerDeathGameEvent;
use collapse\game\event\PlayerKillPlayerGameEvent;
use collapse\PracticeConstants;
use pocketmine\block\Bed;
use pocketmine\block\Block;
use function count;

trait PhaseEventHandlerDefaultImpl{

	public function handlePlayerAttackPlayer(PlayerAttackPlayerGameEvent $event) : void{
		$event->getSubEvent()->cancel();
	}

	public function handlePlayerDamage(PlayerDamageGameEvent $event) : void{
		$event->getSubEvent()->cancel();
	}

	public function handlePlayerDeath(PlayerDeathGameEvent $event) : void{}

	public function handlePlayerKillPlayer(PlayerKillPlayerGameEvent $event) : void{}

	public function handleBlockBreak(BlockBreakGameEvent $event) : void{
		if(!$this->duel->isBlocksActions()){
			$event->cancel();
			return;
		}

		$block = $event->getSubEvent()->getBlock();
		$blockManager = $this->duel->getBlockManager();
		if(!$blockManager->canBreakBlock($block)){
			$event->cancel();
			return;
		}

		if($this->duel instanceof BedsDuel && $block instanceof Bed){
			$event->getSubEvent()->setDrops([]);
			if(!$this->duel->getBedManager()->onBedDestroy($event->getPlayer(), $block)){
				$event->cancel();
			}
			return;
		}

		$item = $blockManager->getBlockItem($block);
		$drops = $event->getSubEvent()->getDrops();
		if(count($drops) > 0){
			foreach($drops as $index => $drop){
				if($item === null || $drop->getTypeId() === $item->getTypeId()){
					$drops[$index] = $item === null ? $drop->setLore([PracticeConstants::ITEM_LORE]) : $item->setCount($drop->getCount());
				}
			}
			$event->getSubEvent()->setDrops($drops);
		}
		$this->duel->getBlockManager()->onBlockBreak($block);
	}

	public function handleBlockPlace(BlockPlaceGameEvent $event) : void{
		if(!$this->duel->isBlocksActions()){
			$event->cancel();
			return;
		}

		$blockManager = $this->duel->getBlockManager();
		$item = $event->getSubEvent()->getItem();
		/** @var Block $block */
		foreach($event->getSubEvent()->getTransaction()->getBlocks() as [, , , $block]){
			$blockManager->onBlockPlace($block, $item);
		}
	}
}

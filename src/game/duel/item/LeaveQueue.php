<?php

declare(strict_types=1);

namespace collapse\game\duel\item;

use collapse\i18n\CollapseTranslationFactory;
use collapse\item\CollapseItem;
use collapse\item\CollapseItemTypeIds;
use collapse\item\component\ItemComponents;
use collapse\item\component\RenderOffsetsComponent;
use collapse\item\DefaultResourcePackItemTrait;
use collapse\item\InventoryItem;
use collapse\item\ResourcePackItem;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use pocketmine\item\ItemIdentifier;

final class LeaveQueue extends CollapseItem implements InventoryItem, ResourcePackItem{
	use DefaultResourcePackItemTrait;

	public function __construct(){
		parent::__construct(new ItemIdentifier(CollapseItemTypeIds::LEAVE_QUEUE), CollapseTranslationFactory::duels_item_leave_queue(), 'Back To Lobby');
	}

	public function onUse(CollapsePlayer $player) : void{
		$queueManager = Practice::getInstance()->getDuelManager()->getQueueManager();
		if($queueManager->isInQueue($player)){
			$queueManager->onPlayerLeaveQueue($player);
		}
	}

	public function addComponents(ItemComponents $components) : ItemComponents{
		return $components->with(RenderOffsetsComponent::create()
			->mainHandFirstPersonScale(RenderOffsetsComponent::calculate(64))
			->mainHandThirdPersonScale(RenderOffsetsComponent::calculate(32))
		);
	}
}

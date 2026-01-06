<?php

declare(strict_types=1);

namespace collapse\system\observe\item;

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

class StopObserve extends CollapseItem implements InventoryItem, ResourcePackItem{
	use DefaultResourcePackItemTrait;

	public function __construct(){
		parent::__construct(new ItemIdentifier(CollapseItemTypeIds::STOP_OBSERVE), CollapseTranslationFactory::command_observe_usage(), 'Stop Observe');
	}

	public function onUse(CollapsePlayer $player) : void{
		$plugin = Practice::getInstance();
		$session = $plugin->getObserveManager()->getSession($player);
		$plugin->getObserveManager()->stopObserving($player);
		$player->sendTranslatedMessage(CollapseTranslationFactory::command_observe_stop($session->getTarget()->getNameWithRankColor()));
	}

	public function addComponents(ItemComponents $components) : ItemComponents{
		return $components->with(RenderOffsetsComponent::create()
			->mainHandFirstPersonScale(RenderOffsetsComponent::calculate(64))
			->mainHandThirdPersonScale(RenderOffsetsComponent::calculate(32))
		);
	}
}

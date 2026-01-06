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

final class Teleport extends CollapseItem implements InventoryItem, ResourcePackItem{
	use DefaultResourcePackItemTrait;

	public function __construct(){
		parent::__construct(new ItemIdentifier(CollapseItemTypeIds::TELEPORT), CollapseTranslationFactory::observe_item_teleport(), 'Teleport');
	}

	public function onUse(CollapsePlayer $player) : void{
		$target = Practice::getInstance()->getObserveManager()->getSession($player)?->getTarget();
		if($target === null){
			return;
		}
		$player->teleport($target->getPosition());
	}

	public function addComponents(ItemComponents $components) : ItemComponents{
		return $components->with(RenderOffsetsComponent::create()
			->mainHandFirstPersonScale(RenderOffsetsComponent::calculate(64))
			->mainHandThirdPersonScale(RenderOffsetsComponent::calculate(32))
		);
	}
}

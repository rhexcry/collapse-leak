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
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemIdentifier;
use pocketmine\network\mcpe\protocol\MobArmorEquipmentPacket;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStack;
use pocketmine\network\mcpe\protocol\types\inventory\ItemStackWrapper;

final class ArmorTakeOff extends CollapseItem implements InventoryItem, ResourcePackItem{
	use DefaultResourcePackItemTrait;

	public function __construct(){
		parent::__construct(new ItemIdentifier(CollapseItemTypeIds::ARMOR_TAKE_OFF), CollapseTranslationFactory::observe_item_armor_take_off(), 'Armor Take Off');
	}

	public function onUse(CollapsePlayer $player) : void{
		$target = Practice::getInstance()->getObserveManager()->getSession($player)?->getTarget();
		if($target === null){
			return;
		}
		$converter = $player->getNetworkSession()->getTypeConverter();
		$player->getNetworkSession()->sendDataPacket(MobArmorEquipmentPacket::create(
			$target->getId(),
			ItemStackWrapper::legacy($converter->coreItemStackToNet(VanillaBlocks::AIR()->asItem())),
			ItemStackWrapper::legacy($converter->coreItemStackToNet(VanillaBlocks::AIR()->asItem())),
			ItemStackWrapper::legacy($converter->coreItemStackToNet(VanillaBlocks::AIR()->asItem())),
			ItemStackWrapper::legacy($converter->coreItemStackToNet(VanillaBlocks::AIR()->asItem())),
			new ItemStackWrapper(0, ItemStack::null())
		));
		$player->sendTranslatedMessage(CollapseTranslationFactory::observe_armor_take_off());
	}

	public function addComponents(ItemComponents $components) : ItemComponents{
		return $components->with(RenderOffsetsComponent::create()
			->mainHandFirstPersonScale(RenderOffsetsComponent::calculate(64))
			->mainHandThirdPersonScale(RenderOffsetsComponent::calculate(32))
		);
	}
}

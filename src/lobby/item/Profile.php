<?php

declare(strict_types=1);

namespace collapse\lobby\item;

use collapse\i18n\CollapseTranslationFactory;
use collapse\item\CollapseItem;
use collapse\item\CollapseItemTypeIds;
use collapse\item\component\ItemComponents;
use collapse\item\component\RenderOffsetsComponent;
use collapse\item\DefaultResourcePackItemTrait;
use collapse\item\InventoryItem;
use collapse\item\ResourcePackItem;
use collapse\player\CollapsePlayer;
use collapse\player\profile\form\ProfileForm;
use pocketmine\item\ItemIdentifier;

final class Profile extends CollapseItem implements InventoryItem, ResourcePackItem{
	use DefaultResourcePackItemTrait;

	public function __construct(){
		parent::__construct(new ItemIdentifier(CollapseItemTypeIds::PROFILE), CollapseTranslationFactory::lobby_item_profile(), 'Profile');
	}

	public function onUse(CollapsePlayer $player) : void{
		$player->sendForm(new ProfileForm($player));
	}

	public function addComponents(ItemComponents $components) : ItemComponents{
		return $components->with(RenderOffsetsComponent::create()
			->mainHandFirstPersonScale(RenderOffsetsComponent::calculate(64))
			->mainHandThirdPersonScale(RenderOffsetsComponent::calculate(32))
		);
	}
}

<?php

declare(strict_types=1);

namespace collapse\system\observe\item;

use collapse\i18n\CollapseTranslationFactory;
use collapse\item\CollapseItem;
use collapse\item\InventoryItem;
use collapse\player\CollapsePlayer;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;

class PunishmentItem extends CollapseItem implements InventoryItem{

	public function __construct(){
		parent::__construct(new ItemIdentifier(ItemTypeIds::NETHERITE_LEGGINGS), CollapseTranslationFactory::command_observe_usage());
	}

	public function onUse(CollapsePlayer $player) : void{
		$player->sendMessage('test', true);
	}
}

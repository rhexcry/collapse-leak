<?php

declare(strict_types=1);

namespace collapse\lobby\item;

use collapse\game\ffa\form\FreeForAllForm;
use collapse\i18n\CollapseTranslationFactory;
use collapse\item\CollapseItem;
use collapse\item\InventoryItem;
use collapse\player\CollapsePlayer;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;

final class FreeForAll extends CollapseItem implements InventoryItem{

	public function __construct(){
		parent::__construct(new ItemIdentifier(ItemTypeIds::IRON_SWORD), CollapseTranslationFactory::lobby_item_free_for_all());
	}

	public function onUse(CollapsePlayer $player) : void{
		$player->sendForm(new FreeForAllForm($player));
	}
}

<?php

declare(strict_types=1);

namespace collapse\lobby\item;

use collapse\game\duel\form\DuelRequestForm;
use collapse\game\duel\form\DuelsForm;
use collapse\i18n\CollapseTranslationFactory;
use collapse\item\CollapseItem;
use collapse\item\InventoryItem;
use collapse\player\CollapsePlayer;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;

final class Duels extends CollapseItem implements InventoryItem{

	public function __construct(){
		parent::__construct(new ItemIdentifier(ItemTypeIds::DIAMOND_SWORD), CollapseTranslationFactory::lobby_item_duels());
	}

	public function onUse(CollapsePlayer $player) : void{
		if(!$player->getCurrentForm() instanceof DuelRequestForm){
			$player->sendForm(new DuelsForm($player));
		}
	}
}

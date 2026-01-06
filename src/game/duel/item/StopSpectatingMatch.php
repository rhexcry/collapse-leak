<?php

declare(strict_types=1);

namespace collapse\game\duel\item;

use collapse\game\duel\Duel;
use collapse\i18n\CollapseTranslationFactory;
use collapse\item\CollapseItem;
use collapse\item\InventoryItem;
use collapse\player\CollapsePlayer;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;

final class StopSpectatingMatch extends CollapseItem implements InventoryItem{

	public function __construct(){
		parent::__construct(new ItemIdentifier(ItemTypeIds::REDSTONE_DUST), CollapseTranslationFactory::duels_item_stop_spectating());
	}

	public function onUse(CollapsePlayer $player) : void{
		$spectatingGame = $player->getSpectatingGame();
		if($spectatingGame instanceof Duel){
			$spectatingGame->getSpectatorManager()->onStoppedSpectating($player);
		}
	}
}

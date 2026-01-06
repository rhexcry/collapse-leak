<?php

declare(strict_types=1);

namespace collapse\game\duel\item;

use collapse\game\duel\inventory\PostMatchInventory;
use collapse\game\duel\records\DuelRecord;
use collapse\i18n\CollapseTranslationFactory;
use collapse\item\CollapseItem;
use collapse\item\WindowInventoryItem;
use collapse\player\CollapsePlayer;
use pocketmine\inventory\Inventory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;

final class ViewPostMatchInventory extends CollapseItem implements WindowInventoryItem{

	private DuelRecord $record;
	private string $xuid;
	private string $player;

	public function __construct(){
		parent::__construct(new ItemIdentifier(ItemTypeIds::ARROW), CollapseTranslationFactory::duels_post_match_inventory_item_view(''));
	}

	public function setRecord(DuelRecord $record) : self{
		$this->record = $record;
		return $this;
	}

	public function setXuid(string $xuid) : self{
		$this->xuid = $xuid;
		return $this;
	}

	public function setPlayer(string $player) : self{
		$this->player = $player;
		return $this;
	}

	public function translate(CollapsePlayer $player) : CollapseItem{
		return $this->setCustomName($player->getProfile()->getTranslator()->translate(CollapseTranslationFactory::duels_post_match_inventory_item_view($this->player)));
	}

	public function onClick(CollapsePlayer $player, Inventory $inventory) : void{
		if(!$inventory instanceof PostMatchInventory){
			return;
		}
		$player->removeCurrentWindow();
		$inventory = new PostMatchInventory($this->record, $this->xuid);
		$inventory->open($player);
	}
}

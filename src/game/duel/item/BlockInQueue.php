<?php

declare(strict_types=1);

namespace collapse\game\duel\item;

use collapse\game\duel\inventory\PostMatchInventory;
use collapse\i18n\CollapseTranslationFactory;
use collapse\item\CollapseItem;
use collapse\item\CollapseItemTypeIds;
use collapse\item\component\ItemComponents;
use collapse\item\component\RenderOffsetsComponent;
use collapse\item\DefaultResourcePackItemTrait;
use collapse\item\ResourcePackItem;
use collapse\item\WindowInventoryItem;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use pocketmine\inventory\Inventory;
use pocketmine\item\ItemIdentifier;

final class BlockInQueue extends CollapseItem implements WindowInventoryItem, ResourcePackItem{
	use DefaultResourcePackItemTrait;

	private string $xuid;

	public function __construct(){
		parent::__construct(new ItemIdentifier(CollapseItemTypeIds::BLOCK_IN_QUEUE), CollapseTranslationFactory::duels_post_match_inventory_item_view(''), 'Block In Queue');
	}

	public function setXuid(string $xuid) : self{
		$this->xuid = $xuid;
		return $this;
	}

	public function translate(CollapsePlayer $player) : CollapseItem{
		return $this->setCustomName($player->getProfile()->getTranslator()->translate(CollapseTranslationFactory::duels_item_block_in_queue()));
	}

	public function onClick(CollapsePlayer $player, Inventory $inventory) : void{
		if(!$inventory instanceof PostMatchInventory){
			return;
		}

		$opponentProfile = Practice::getInstance()->getProfileManager()->getProfileByXuid($this->xuid);

		if($player->getProfile()->isProfileBannedInQueue($opponentProfile)){
			return;
		}

		$player->getProfile()->addBannedProfileInQueue($opponentProfile);

	}

	public function addComponents(ItemComponents $components) : ItemComponents{
		return $components->with(RenderOffsetsComponent::create()
			->mainHandFirstPersonScale(RenderOffsetsComponent::calculate(64))
			->mainHandThirdPersonScale(RenderOffsetsComponent::calculate(32))
		);
	}
}

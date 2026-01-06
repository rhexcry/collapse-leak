<?php

declare(strict_types=1);

namespace collapse\game\ffa\item;

use collapse\game\ffa\FreeForAllArena;
use collapse\i18n\CollapseTranslationFactory;
use collapse\item\CollapseItem;
use collapse\item\CollapseItemTypeIds;
use collapse\item\component\ItemComponents;
use collapse\item\component\RenderOffsetsComponent;
use collapse\item\DefaultResourcePackItemTrait;
use collapse\item\InventoryItem;
use collapse\item\ResourcePackItem;
use collapse\player\CollapsePlayer;
use pocketmine\item\ItemIdentifier;

final class RespawnItem extends CollapseItem implements InventoryItem, ResourcePackItem{
	use DefaultResourcePackItemTrait;

	public function __construct(){
		parent::__construct(new ItemIdentifier(CollapseItemTypeIds::REBORN), CollapseTranslationFactory::free_for_all_item_respawn(), 'Reborn');
	}

	public function onUse(CollapsePlayer $player) : void{
		$arena = $player->getGame();
		if(!$arena instanceof FreeForAllArena){
			return;
		}
		$spawnLocation = $arena->getConfig()->getSpawnLocation();
		if($arena->hasRandomSpawn()){
			/** @noinspection PhpPossiblePolymorphicInvocationInspection */
			$spawnLocation = $arena->getRandomSpawn();
		}
		$player->teleport($spawnLocation);
		$arena->getPlayerManager()->reset($player);
	}

	public function addComponents(ItemComponents $components) : ItemComponents{
		return $components->with(RenderOffsetsComponent::create()
			->mainHandFirstPersonScale(RenderOffsetsComponent::calculate(64))
			->mainHandThirdPersonScale(RenderOffsetsComponent::calculate(32))
		);
	}
}

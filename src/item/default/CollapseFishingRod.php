<?php

declare(strict_types=1);

namespace collapse\item\default;

use collapse\entity\FishingHook;
use collapse\player\CollapsePlayer;
use pocketmine\entity\Location;
use pocketmine\item\FishingRod;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\sound\ThrowSound;

final class CollapseFishingRod extends FishingRod{

	public function __construct(){
		parent::__construct(new ItemIdentifier(ItemTypeIds::FISHING_ROD));
	}

	/**
	 * @param CollapsePlayer $player
	 */
	public function onClickAir(Player $player, Vector3 $directionVector, array &$returnedItems) : ItemUseResult{
		if($player->getFishingHook() === null){
			$hook = new FishingHook(Location::fromObject($player->getEyePos()->add(0, -1.0, 0), $player->getWorld()), $player);
			$motion = $player->getDirectionVector()->multiply(0.4);
			$hook->spawnToAll();
			$hook->setMotion($motion);
			$hook->handleHookCasting($motion->x, $motion->y, $motion->z, 2.5, 2.5);
			$player->setFishingHook($hook);
			$player->broadcastSound(new ThrowSound());
		}else{
			$hook = $player->getFishingHook();
			$hook->handleHookRetraction();
		}

		return ItemUseResult::SUCCESS;
	}
}

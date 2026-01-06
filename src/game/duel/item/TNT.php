<?php

declare(strict_types=1);

namespace collapse\game\duel\item;

use collapse\game\duel\entity\TNT as EntityTNT;
use collapse\i18n\CollapseTranslationFactory;
use collapse\item\CollapseItem;
use collapse\player\CollapsePlayer;
use pocketmine\block\Block;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Location;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\Random;
use pocketmine\world\sound\IgniteSound;
use function cos;
use function sin;
use const M_PI;

final class TNT extends CollapseItem{

	public function __construct(){
		parent::__construct(new ItemIdentifier(-BlockTypeIds::TNT), CollapseTranslationFactory::duels_item_tnt(), block: VanillaBlocks::TNT());
	}

	/**
	 * @var CollapsePlayer $player
	 */
	public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, array &$returnedItems) : ItemUseResult{
		if(!$player->isInGame()){
			return ItemUseResult::FAIL;
		}

		$mot = (new Random())->nextSignedFloat() * M_PI * 2;

		$tnt = new EntityTNT(Location::fromObject($blockReplace->getPosition()->add(0.5, 0, 0.5), $player->getWorld()));
		$tnt->setGame($player->getGame());
		$tnt->setFuse(60);
		$tnt->setMotion(new Vector3(-sin($mot) * 0.02, 0.2, -cos($mot) * 0.02));

		$tnt->spawnToAll();
		$tnt->broadcastSound(new IgniteSound());
		$this->pop();
		$player->getInventory()->setItemInHand($this->isNull() ? VanillaBlocks::AIR()->asItem() : $this);
		return ItemUseResult::NONE;
	}
}

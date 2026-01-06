<?php

declare(strict_types=1);

namespace collapse\item;

use pocketmine\item\ItemTypeIds;

final readonly class CollapseItemTypeIds{

	public const int TELEPORT = ItemTypeIds::FIRST_UNUSED_ITEM_ID + 1;
	public const int ARMOR_TAKE_OFF = ItemTypeIds::FIRST_UNUSED_ITEM_ID + 2;
	public const int STOP_OBSERVE = ItemTypeIds::FIRST_UNUSED_ITEM_ID + 3;

	public const int PROFILE = ItemTypeIds::FIRST_UNUSED_ITEM_ID + 4;
	public const int SHOP = ItemTypeIds::FIRST_UNUSED_ITEM_ID + 5;
	public const int COSMETICS = ItemTypeIds::FIRST_UNUSED_ITEM_ID + 6;
	public const int LEADERBOARDS = ItemTypeIds::FIRST_UNUSED_ITEM_ID + 7;
	public const int BLOCK_IN_QUEUE = ItemTypeIds::FIRST_UNUSED_ITEM_ID + 8;
	public const int QUESTS = ItemTypeIds::FIRST_UNUSED_ITEM_ID + 9;
	public const int REBORN = ItemTypeIds::FIRST_UNUSED_ITEM_ID + 10;
	public const int BACK_TO_LOBBY = ItemTypeIds::FIRST_UNUSED_ITEM_ID + 11;
	public const int LEAVE_QUEUE = ItemTypeIds::FIRST_UNUSED_ITEM_ID + 12;
}

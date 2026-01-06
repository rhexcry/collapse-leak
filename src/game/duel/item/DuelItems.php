<?php

declare(strict_types=1);

namespace collapse\game\duel\item;

use collapse\utils\ItemUtils;
use pocketmine\item\Item;
use pocketmine\utils\CloningRegistryTrait;

/**
 * @method static LeaveQueue LEAVE_QUEUE()
 * @method static StopSpectatingMatch STOP_SPECTATING_MATCH()
 * @method static ViewPostMatchInventory VIEW_POST_MATCH_INVENTORY()
 * @method static TNT TNT()
 * @method static GoldenHead GOLDEN_HEAD()
 * @method static BlockInQueue BLOCK_IN_QUEUE()
 */
final class DuelItems{
	use CloningRegistryTrait;

	private static function register(string $name, Item $item) : void{
		self::_registryRegister($name, $item);
	}

	protected static function setup() : void{
		ItemUtils::registerResourcePackItem(new LeaveQueue());
		self::register('leave_queue', new LeaveQueue());

		self::register('stop_spectating_match', new StopSpectatingMatch());
		self::register('view_post_match_inventory', new ViewPostMatchInventory());
		self::register('tnt', new TNT());
		self::register('golden_head', new GoldenHead());

		ItemUtils::registerResourcePackItem(NEW BlockInQueue());
		self::register('block_in_queue', new BlockInQueue());

	}
}

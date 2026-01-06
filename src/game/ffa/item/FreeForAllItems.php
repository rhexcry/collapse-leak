<?php

declare(strict_types=1);

namespace collapse\game\ffa\item;

use collapse\item\CollapseItem;
use collapse\utils\ItemUtils;
use pocketmine\utils\CloningRegistryTrait;

/**
 * @method static RespawnItem RESPAWN()
 * @method static LobbyItem LOBBY()
 */
final class FreeForAllItems{
	use CloningRegistryTrait;

	private static function register(string $name, CollapseItem $item) : void{
		self::_registryRegister($name, $item);
	}

	protected static function setup() : void{
		ItemUtils::registerResourcePackItem(new RespawnItem());
		self::register('respawn', new RespawnItem());

		ItemUtils::registerResourcePackItem(new LobbyItem());
		self::register('lobby', new LobbyItem());
	}

	public static function init() : void{
		self::checkInit();
	}
}

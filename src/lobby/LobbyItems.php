<?php

declare(strict_types=1);

namespace collapse\lobby;

use collapse\item\CollapseItem;
use collapse\lobby\item\Cosmetics;
use collapse\lobby\item\Duels;
use collapse\lobby\item\FreeForAll;
use collapse\lobby\item\Leaderboards;
use collapse\lobby\item\Profile;
use collapse\lobby\item\Quests;
use collapse\lobby\item\Shop;
use collapse\utils\ItemUtils;
use pocketmine\utils\CloningRegistryTrait;

/**
 * @method static Duels DUELS()
 * @method static FreeForAll FREE_FOR_ALL()
 * @method static Cosmetics COSMETICS()
 * @method static Leaderboards LEADERBOARDS()
 * @method static Profile PROFILE()
 * @method static Shop SHOP()
 * @method static Quests QUESTS()
 */
class LobbyItems{
	use CloningRegistryTrait;

	private static function register(string $name, CollapseItem $item) : void{
		self::_registryRegister($name, $item);
	}

	protected static function setup() : void{
		self::register('duels', new Duels());
		self::register('free_for_all', new FreeForAll());

		ItemUtils::registerResourcePackItem(new Cosmetics());
		self::register('cosmetics', new Cosmetics());

		ItemUtils::registerResourcePackItem(new Leaderboards());
		self::register('leaderboards', new Leaderboards());

		ItemUtils::registerResourcePackItem(new Profile());
		self::register('profile', new Profile());

		ItemUtils::registerResourcePackItem(new Shop());
		self::register('shop', new Shop());

		ItemUtils::registerResourcePackItem(new Quests());
		self::register('quests', new Quests());
	}

	public static function init() : void{
		self::checkInit();
	}
}

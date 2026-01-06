<?php

declare(strict_types=1);

namespace collapse\system\observe\item;

use collapse\item\CollapseItem;
use collapse\utils\ItemUtils;
use pocketmine\utils\CloningRegistryTrait;

/**
 * @method static Teleport TELEPORT()
 * @method static ArmorTakeOff ARMOR_TAKE_OFF()
 * @method static StopObserve STOP_OBSERVE()
 */
final class ObserveItems{
	use CloningRegistryTrait;

	private static function register(string $name, CollapseItem $item) : void{
		self::_registryRegister($name, $item);
	}

	public static function init() : void{
		self::checkInit();
	}

	protected static function setup() : void{
		ItemUtils::registerResourcePackItem(new Teleport());
		self::register('teleport', new Teleport());
		ItemUtils::registerResourcePackItem(new ArmorTakeOff());
		self::register('armor_take_off', new ArmorTakeOff());
		ItemUtils::registerResourcePackItem(new StopObserve());
		self::register('stop_observe', new StopObserve());
	}
}

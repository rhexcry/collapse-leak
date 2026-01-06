<?php

declare(strict_types=1);

namespace collapse\wallet\currency;

use pocketmine\utils\RegistryTrait;
use function mb_strtoupper;

/**
 * @method static StarCurrency STAR()
 * @method static DustCurrency DUST()
 */
final class Currencies{
	use RegistryTrait;

	private static function register(string $member, Currency $currency) : void{
		self::_registryRegister($member, $currency);
	}

	public static function setup() : void{
		self::register('star', new StarCurrency());
		self::register('dust', new DustCurrency());
	}

	public static function get(string $name) : ?Currency{
		self::checkInit();
		self::verifyName($name);
		$upperName = mb_strtoupper($name);
		return self::$members[$upperName] ?? null;
	}

	/**
	 * @return Currency[]
	 */
	public static function getAll() : array{
		self::checkInit();
		return self::$members;
	}
}

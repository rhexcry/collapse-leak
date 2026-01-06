<?php

declare(strict_types=1);

namespace collapse\game\ffa;

use collapse\game\ffa\types\FreeForAllMode;
use collapse\game\kb\KnockBack;
use pocketmine\utils\RegistryTrait;

/**
 * @method static KnockBack NoDebuff()
 * @method static KnockBack GAPPLE()
 * @method static KnockBack SUMO()
 * @method static KnockBack BUILD()
 * @method static KnockBack CRYSTAL()
 * @method static KnockBack RESISTANCE()
 */
final class FreeForAllKnockBacks{
	use RegistryTrait;

	private static function register(FreeForAllMode $mode, KnockBack $knockBack) : void{
		self::_registryRegister($mode->value, $knockBack);
	}

	protected static function setup() : void{
		self::register(FreeForAllMode::NoDebuff, new KnockBack(0.399, 0.4, 9));
		self::register(FreeForAllMode::GApple, new KnockBack(0.399, 0.4, 9));
		self::register(FreeForAllMode::Sumo, new KnockBack(0.399, 0.4, 9));
		self::register(FreeForAllMode::Build, new KnockBack(0.399, 0.4, 9));
		self::register(FreeForAllMode::Crystal, new KnockBack(0.399, 0.4, 10));
		self::register(FreeForAllMode::Resistance, new KnockBack(0.399, 0.4, 9));
		self::register(FreeForAllMode::MidFight, new KnockBack(0.399, 0.4, 10));
	}

	/**
	 * @return KnockBack
	 */
	public static function get(FreeForAllMode $mode) : object{
		return self::_registryFromString($mode->value);
	}
}

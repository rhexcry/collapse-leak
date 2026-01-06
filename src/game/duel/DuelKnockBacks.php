<?php

declare(strict_types=1);

namespace collapse\game\duel;

use collapse\game\duel\types\DuelMode;
use collapse\game\kb\KnockBack;
use pocketmine\utils\RegistryTrait;

/**
 * @method static KnockBack NO_DEBUFF()
 * @method static KnockBack FIREBALL_FIGHT()
 * @method static KnockBack BUILDUHC()
 * @method static KnockBack SKYWARS()
 * @method static KnockBack GAPPLE()
 */
final class DuelKnockBacks{
	use RegistryTrait;

	private static function register(DuelMode $mode, KnockBack $knockBack) : void{
		self::_registryRegister($mode->value, $knockBack);
	}

	protected static function setup() : void{
		self::register(DuelMode::NoDebuff, new KnockBack(0.399, 0.4, 9));
		self::register(DuelMode::FireballFight, new KnockBack(0.399, 0.4, 9));
		self::register(DuelMode::BuildUHC, new KnockBack(0.399, 0.4, 9));
		self::register(DuelMode::GApple, new KnockBack(0.399, 0.4, 9));
		//self::register(DuelMode::SkyWars, new KnockBack(0.382, 0.372, 10));
	}

	/**
	 * @return KnockBack
	 */
	public static function get(DuelMode $mode) : object{
		return self::_registryFromString($mode->value);
	}
}

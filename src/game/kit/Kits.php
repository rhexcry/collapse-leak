<?php

declare(strict_types=1);

namespace collapse\game\kit;

use collapse\game\kit\modes\Build;
use collapse\game\kit\modes\BuildUHC;
use collapse\game\kit\modes\Crystal;
use collapse\game\kit\modes\FireballFight;
use collapse\game\kit\modes\GApple;
use collapse\game\kit\modes\MidFight;
use collapse\game\kit\modes\NoDebuff;
use collapse\game\kit\modes\Resistance;
use collapse\game\kit\modes\SkyWars;
use collapse\game\kit\modes\Sumo;
use pocketmine\utils\RegistryTrait;

/**
 * @method static NoDebuff NO_DEBUFF()
 * @method static FireballFight FIREBALL_FIGHT()
 * @method static GApple GAPPLE()
 * @method static Sumo SUMO()
 * @method static BuildUHC BUILDUHC()
 * @method static Build BUILD()
 * @method static Crystal CRYSTAL()
 * @method static Resistance RESISTANCE()
 * @method static MidFight MIDFIGHT()
 * @method static SkyWars SKYWARS()
 */
final class Kits{
	use RegistryTrait;

	private static function register(Kit $kit, KitCollection $collection) : void{
		$collection->setType($kit);
		self::_registryRegister($kit->value, $collection);
	}

	protected static function setup() : void{
		self::register(Kit::NoDebuff, new NoDebuff());
		self::register(Kit::FireballFight, new FireballFight());
		self::register(Kit::GApple, new GApple());
		self::register(Kit::Sumo, new Sumo());
		self::register(Kit::BuildUHC, new BuildUHC());
		self::register(Kit::Build, new Build());
		self::register(Kit::Crystal, new Crystal());
		self::register(Kit::Resistance, new Resistance());
		self::register(Kit::MidFight, new MidFight());
		self::register(Kit::SkyWars, new SkyWars());
	}

	/**
	 * @return KitCollection
	 */
	public static function get(Kit $kit) : object{
		return self::_registryFromString($kit->value);
	}
}

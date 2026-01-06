<?php

declare(strict_types=1);

namespace collapse\game\ffa\types;

use collapse\game\ffa\FreeForAllArena;
use collapse\game\ffa\FreeForAllConfig;
use collapse\game\ffa\modes\build\Build;
use collapse\game\ffa\modes\crystal\Crystal;
use collapse\game\ffa\modes\GApple;
use collapse\game\ffa\modes\midfight\MidFight;
use collapse\game\ffa\modes\NoDebuff;
use collapse\game\ffa\modes\resistance\Resistance;
use collapse\game\ffa\modes\sumo\Sumo;
use collapse\game\kit\Kit;
use collapse\Practice;
use pocketmine\utils\TextFormat;

enum FreeForAllMode : string{

	case NoDebuff = 'no_debuff';
	case GApple = 'gapple';
	case Sumo = 'sumo';
	case Build = 'build';
	case Crystal = 'crystal';
	case Resistance = 'resistance';
	case MidFight = 'midfight';

	public function toDisplayName() : string{
		return match($this){
			self::NoDebuff => TextFormat::RED . 'No Debuff',
			self::GApple => TextFormat::YELLOW . 'GApple',
			self::Sumo => TextFormat::GREEN . 'Sumo',
			self::Build => TextFormat::BLUE . 'Build',
			self::Crystal => TextFormat::DARK_PURPLE . 'Crystal PvP',
			self::Resistance => TextFormat::AQUA . 'Resistance',
			self::MidFight => TextFormat::WHITE . 'Mid' . TextFormat::RED . 'Fight',
		};
	}

	public function toKit() : Kit{
		return match($this){
			self::NoDebuff => Kit::NoDebuff,
			self::GApple => Kit::GApple,
			self::Sumo => Kit::Sumo,
			self::Build => Kit::Build,
			self::Crystal => Kit::Crystal,
			self::Resistance => Kit::Resistance,
			self::MidFight => Kit::MidFight
		};
	}

	public function create(Practice $plugin, FreeForAllConfig $config) : FreeForAllArena{
		return match($this){
			self::NoDebuff => new NoDebuff($plugin, $config),
			self::GApple => new GApple($plugin, $config),
			self::Sumo => new Sumo($plugin, $config),
			self::Build => new Build($plugin, $config),
			self::Crystal => new Crystal($plugin, $config),
			self::Resistance => new Resistance($plugin, $config),
			self::MidFight => new MidFight($plugin, $config)
		};
	}

	public function toTexture() : string{
		return match($this){
			self::NoDebuff => 'nodebuff',
			self::GApple => 'gapple',
			self::Sumo => 'sumo',
			self::Build => 'build',
			self::Crystal => 'crystal',
			self::Resistance => 'resistance',
			self::MidFight => 'midfight',
		};
	}
}

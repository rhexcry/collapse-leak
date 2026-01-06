<?php

declare(strict_types=1);

namespace collapse\game\kit;

enum Kit : string{

	case NoDebuff = 'no_debuff';

	case FireballFight = 'fireball_fight';

	case GApple = 'gapple';

	case Sumo = 'sumo';

	case BuildUHC = 'builduhc';

	case Build = 'build';

	case Crystal = 'crystal';

	case Resistance = 'resistance';

	case MidFight = 'midfight';
	case SkyWars = 'skywars';

	public function toDisplayName() : string{
		return match ($this) {
			self::NoDebuff => 'No Debuff',
			self::FireballFight => 'Fireball Fight',
			self::GApple => 'GApple',
			self::Sumo => 'Sumo',
			self::BuildUHC => 'Build UHC',
			self::Build => 'Build FFA',
			self::Crystal => 'Crystal',
			self::Resistance => 'Resistance',
			self::MidFight => 'Mid Fight',
			self::SkyWars => 'SkyWars'
		};
	}
}

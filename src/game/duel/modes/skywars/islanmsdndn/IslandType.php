<?php

declare(strict_types=1);

namespace collapse\game\duel\modes\skywars\islanmsdndn;

use collapse\game\duel\modes\skywars\SkyWarsChestType;

enum IslandType : string{
	case START = 'start';
	case MID = 'mid';
	case SPECIAL = 'special';

	public function getDisplayName() : string{
		return match ($this) {
			self::START => 'Стартовый остров',
			self::MID => 'Центральный остров',
			self::SPECIAL => 'Особый остров'
		};
	}

	public function getChestType() : SkyWarsChestType{
		return match ($this) {
			self::START => SkyWarsChestType::BASIC,
			self::MID => SkyWarsChestType::MID,
			self::SPECIAL => SkyWarsChestType::OP
		};
	}
}
<?php

declare(strict_types=1);

namespace collapse\game\duel\modes\skywars;


enum SkyWarsChestType : string{
	case BASIC = 'basic';
	case MID = 'mid';
	case OP = 'op';

	public function getDisplayName() : string{
		return match ($this) {
			self::BASIC => 'Обычный сундук',
			self::MID => 'Центральный сундук',
			self::OP => 'OP сундук'
		};
	}
}
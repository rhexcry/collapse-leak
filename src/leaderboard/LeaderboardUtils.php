<?php

declare(strict_types=1);

namespace collapse\leaderboard;

use pocketmine\utils\TextFormat;
use function number_format;

final readonly class LeaderboardUtils{

	private function __construct(){}

	public static function getRankColor(int $rank) : string{
		return match($rank){
			1 => TextFormat::MINECOIN_GOLD,
			2 => TextFormat::MATERIAL_IRON,
			3 => TextFormat::MATERIAL_COPPER,
			default => TextFormat::DARK_GRAY
		};
	}

	public static function simpleFormatProfileLeaderboardEntry(ProfileLeaderboardEntry $entry) : string{
		return self::getRankColor($entry->getRank()) . '#' . $entry->getRank() . ' ' .
			$entry->getProfile()->getRank()->toColor() . $entry->getProfile()->getPlayerName() .
			TextFormat::GRAY . ' - ' .
			number_format($entry->getValue());
	}
}

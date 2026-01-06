<?php

declare(strict_types=1);

namespace collapse\game\teams;

use pocketmine\block\utils\DyeColor;

final readonly class TeamUtils{

	private function __construct(){}

	public static function bedToTeamId(DyeColor $color) : ?string{
		return match($color){
			DyeColor::RED => Teams::TEAM_RED,
			DyeColor::BLUE => Teams::TEAM_BLUE,
			default => null
		};
	}
}

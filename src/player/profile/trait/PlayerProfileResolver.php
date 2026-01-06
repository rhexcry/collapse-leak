<?php

declare(strict_types=1);

namespace collapse\player\profile\trait;

use collapse\player\CollapsePlayer;
use collapse\player\profile\Profile;
use collapse\Practice;
use function strtolower;

trait PlayerProfileResolver{

	private static function resolveProfile(CollapsePlayer|string $player) : ?Profile{
		if($player instanceof CollapsePlayer){
			return $player->getProfile();
		}

		return Practice::getInstance()
			->getProfileManager()
			->getProfileByName(strtolower($player));
	}

	private static function resolveProfileByXuid(CollapsePlayer|string $player) : ?Profile{
		if($player instanceof CollapsePlayer){
			return $player->getProfile();
		}

		return Practice::getInstance()
			->getProfileManager()
			->getProfileByXuid($player);
	}
}

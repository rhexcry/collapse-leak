<?php

declare(strict_types=1);

namespace collapse\game\ffa\modes;

use collapse\game\ffa\FreeForAllArena;
use collapse\game\statistics\GameStatistics;
use collapse\game\statistics\GameStatisticsManager;
use collapse\player\CollapsePlayer;
use collapse\utils\TextUtils;
use pocketmine\utils\TextFormat;

final class NoDebuff extends FreeForAllArena{

	protected function addAdditionalPlayerTags(CollapsePlayer $player, ?GameStatisticsManager $manager) : array{
		return [
			TextFormat::RED . ($pots = $manager->get(GameStatistics::TOTAL_POTIONS)->get($player) - $manager->get(GameStatistics::THROW_POTIONS)->get($player)) . ' ' . TextUtils::convert($pots, 'POT;POTS;POTS')
		];
	}

	protected function addAdditionalKillerTags(CollapsePlayer $player, ?GameStatisticsManager $manager) : array{
		return [
			TextFormat::RED . ($pots = $manager->get(GameStatistics::TOTAL_POTIONS)->get($player) - $manager->get(GameStatistics::THROW_POTIONS)->get($player)) . ' ' . TextUtils::convert($pots, 'POT;POTS;POTS')
		];
	}

	public function isEnderPearlCooldown() : bool{
		return true;
	}

	public function isStatisticsEnabled() : bool{
		return true;
	}

	public function isAntiInterrupt() : bool{
		return true;
	}

	public function isCombat() : bool{
		return true;
	}

	public function isHidePlayersInCombat() : bool{
		return true;
	}
}

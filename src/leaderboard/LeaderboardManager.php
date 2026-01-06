<?php

declare(strict_types=1);

namespace collapse\leaderboard;

use collapse\leaderboard\types\DuelsRankedElo;
use collapse\leaderboard\types\DuelsUnrankedBestWinStreak;
use collapse\leaderboard\types\DuelsUnrankedWins;
use collapse\leaderboard\types\FreeForAllKills;
use collapse\Practice;

final class LeaderboardManager{

	/** @var Leaderboard[] */
	private array $leaderboards = [];

	public function __construct(
		private readonly Practice $plugin
	){
		$this->register(new DuelsRankedElo());
		$this->register(new DuelsUnrankedWins());
		$this->register(new DuelsUnrankedBestWinStreak());
		$this->register(new FreeForAllKills());
	}

	public function getPlugin() : Practice{
		return $this->plugin;
	}

	private function register(Leaderboard $leaderboard) : void{
		$this->leaderboards[$leaderboard->getType()->name] = $leaderboard;
		$leaderboard->update();
	}

	public function getLeaderboard(LeaderboardType $type) : ?Leaderboard{
		return $this->leaderboards[$type->name] ?? null;
	}
}

<?php

declare(strict_types=1);

namespace collapse\leaderboard;

enum LeaderboardType{

	case DuelsRankedElo;

	case DuelsUnrankedWins;

	case DuelsUnrankedBestWinStreak;

	case FreeForAllKills;
}

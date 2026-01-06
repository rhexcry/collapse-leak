<?php

declare(strict_types=1);

namespace collapse\leaderboard;

abstract class Leaderboard{

	/** @var (LeaderboardEntry[]|LeaderboardEntry[][]|null) */
	protected ?array $entries = null;

	abstract public function getType() : LeaderboardType;

	abstract public function update() : void;
}

<?php

declare(strict_types=1);

namespace collapse\leaderboard;

abstract readonly class LeaderboardEntry{

	public function __construct(
		private string $name,
		private int|float $value,
		private int $rank
	){}

	public function getName() : string{
		return $this->name;
	}

	public function getValue() : int|float{
		return $this->value;
	}

	public function getRank() : int{
		return $this->rank;
	}
}

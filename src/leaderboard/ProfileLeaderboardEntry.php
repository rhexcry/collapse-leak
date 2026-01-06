<?php

declare(strict_types=1);

namespace collapse\leaderboard;

use collapse\player\profile\Profile;

final readonly class ProfileLeaderboardEntry extends LeaderboardEntry{

	public function __construct(
		private Profile $profile,
		string $name,
		float|int $value,
		int $rank
	){
		parent::__construct($name, $value, $rank);
	}

	public function getProfile() : Profile{
		return $this->profile;
	}
}

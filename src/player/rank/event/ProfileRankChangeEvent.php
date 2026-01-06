<?php

declare(strict_types=1);

namespace collapse\player\rank\event;

use collapse\player\profile\event\ProfileEvent;
use collapse\player\profile\Profile;
use collapse\player\rank\Rank;

final class ProfileRankChangeEvent extends ProfileEvent{

	public function __construct(
		Profile $profile,
		private readonly Rank $rank
	){
		parent::__construct($profile);
	}

	public function getRank() : Rank{
		return $this->rank;
	}
}

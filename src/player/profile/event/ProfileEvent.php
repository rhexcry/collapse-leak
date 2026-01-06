<?php

declare(strict_types=1);

namespace collapse\player\profile\event;

use collapse\player\profile\Profile;
use pocketmine\event\Event;

abstract class ProfileEvent extends Event{

	public function __construct(
		private readonly Profile $profile
	){}

	public function getProfile() : Profile{
		return $this->profile;
	}
}

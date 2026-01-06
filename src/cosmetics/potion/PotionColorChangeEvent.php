<?php

declare(strict_types=1);

namespace collapse\cosmetics\potion;

use collapse\player\profile\Profile;
use pocketmine\event\Event;

final class PotionColorChangeEvent extends Event{

	public function __construct(
		private readonly Profile $profile,
		private readonly ?PotionColor $color
	){}

	public function getProfile() : Profile{
		return $this->profile;
	}

	public function getColor() : ?PotionColor{
		return $this->color;
	}
}
<?php

declare(strict_types=1);

namespace collapse\cosmetics\effects\death;

use collapse\player\profile\event\ProfileEvent;
use collapse\player\profile\Profile;

final class DeathEffectChangeEvent extends ProfileEvent{

	public function __construct(
		Profile $profile,
		private readonly ?DeathEffectType $deathEffect
	){
		parent::__construct($profile);
	}

	public function getDeathEffect() : ?DeathEffectType{
		return $this->deathEffect;
	}
}

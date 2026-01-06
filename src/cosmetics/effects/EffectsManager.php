<?php

declare(strict_types=1);

namespace collapse\cosmetics\effects;

use collapse\cosmetics\effects\death\DeathEffectType;
use collapse\player\profile\Profile;
use collapse\Practice;

final readonly class EffectsManager{

	public function __construct(
		private Practice $plugin
	){}

	public function setDeathEffect(Profile $profile, ?DeathEffectType $deathEffect) : void{
		$profile->setDeathEffect($deathEffect);
		$profile->save();
	}
}

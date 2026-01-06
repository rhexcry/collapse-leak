<?php

declare(strict_types=1);

namespace collapse\cooldown;

use collapse\cooldown\types\CooldownType;

interface Cooldown{

	public function getType() : CooldownType;

	public function onStart() : void;

	public function onCompletion() : void;

	public function isActive() : bool;
}

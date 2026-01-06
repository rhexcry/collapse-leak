<?php

declare(strict_types=1);

namespace collapse\cooldown\types;

use collapse\cooldown\Cooldown;
use function microtime;

class Command implements Cooldown{

	private float $expires;

	public function __construct(
		private readonly float $milliseconds
	){}

	public function getType() : CooldownType{
		return CooldownType::Command;
	}

	public function onStart() : void{
		$this->expires = microtime(true) + $this->milliseconds;
	}

	public function onCompletion() : void{}

	public function isActive() : bool{
		return $this->expires > microtime(true);
	}
}

<?php

declare(strict_types=1);

namespace collapse\game\kb;

final readonly class KnockBack{

	public function __construct(
		private float $horizontal,
		private float $vertical,
		private int $attackCooldown
	){}

	public function getHorizontal() : float{
		return $this->horizontal;
	}

	public function getVertical() : float{
		return $this->vertical;
	}

	public function getAttackCooldown() : int{
		return $this->attackCooldown;
	}
}

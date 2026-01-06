<?php

declare(strict_types=1);

namespace collapse\system\party;

use collapse\player\CollapsePlayer;

final class PartyInvite{

	private const int EXPIRATION = 60 * 20;

	public function __construct(
		private CollapsePlayer $player,
		private CollapsePlayer $target
	){}

	public function getPlayer() : CollapsePlayer{
		return $this->player;
	}

	public function getTarget() : CollapsePlayer{
		return $this->target;
	}
}
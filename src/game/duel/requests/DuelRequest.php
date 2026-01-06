<?php

declare(strict_types=1);

namespace collapse\game\duel\requests;

use collapse\game\duel\types\DuelMode;
use function time;

final readonly class DuelRequest{

	private int $expires;

	public function __construct(
		private string $playerXuid,
		private string $senderXuid,
		private DuelMode $mode
	){
		$this->expires = time() + DuelRequestManager::EXPIRES_TIME;
	}

	public function getPlayerXuid() : string{
		return $this->playerXuid;
	}

	public function getSenderXuid() : string{
		return $this->senderXuid;
	}

	public function getMode() : DuelMode{
		return $this->mode;
	}

	public function isExpired() : bool{
		return time() > $this->expires;
	}
}

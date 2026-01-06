<?php

declare(strict_types=1);

namespace collapse\system\friend;

final readonly class Friend{

	public function __construct(
		private string $xuid,
		private int $since
	){}

	public function getXuid() : string{
		return $this->xuid;
	}

	public function getSince() : int{
		return $this->since;
	}
}

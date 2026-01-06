<?php

declare(strict_types=1);

namespace collapse\network\rcon;

final readonly class RconConfig{
	public function __construct(
		public string $ip,
		public int $port,
		public int $maxConnections,
		public string $password
	){}
}

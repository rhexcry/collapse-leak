<?php

declare(strict_types=1);

namespace collapse\feature;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class EventSubscribe{

	public function __construct(
		public string $eventClass,
		public int $priority = 50
	){
	}
}

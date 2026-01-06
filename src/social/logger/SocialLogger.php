<?php

declare(strict_types=1);

namespace collapse\social\logger;

use collapse\social\SocialManager;

abstract readonly class SocialLogger{

	public function __construct(
		protected SocialManager $socialManager
	){}
}

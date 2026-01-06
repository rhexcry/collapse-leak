<?php

declare(strict_types=1);

namespace collapse\social;

use collapse\social\request\SocialRequest;

interface Social{

	public function log(SocialRequest $request) : void;

}

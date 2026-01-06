<?php

declare(strict_types=1);

namespace collapse\social\request;

abstract class SocialRequest{

	abstract public function execute() : void;

}

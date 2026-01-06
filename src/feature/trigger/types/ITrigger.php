<?php

declare(strict_types=1);

namespace collapse\feature\trigger\types;

interface ITrigger{

	public function execute(object $event) : void;
}

<?php

declare(strict_types=1);

namespace collapse\feature\trigger\types;

abstract class EventTrigger extends BaseTrigger{

	abstract public function shouldHandle(object $event) : bool;

	abstract public function getHandleableEvents() : array;

}

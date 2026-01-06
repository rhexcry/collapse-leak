<?php

declare(strict_types=1);

namespace collapse\system\clan\event;

use collapse\system\clan\concrete\Clan;
use pocketmine\event\Event;

class ClanEvent extends Event{

	public function __construct(protected readonly Clan $clan){}

	public function getClan() : Clan{
		return $this->clan;
	}
}

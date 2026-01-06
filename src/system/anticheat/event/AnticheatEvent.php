<?php

declare(strict_types=1);

namespace collapse\system\anticheat\event;

use collapse\system\anticheat\AnticheatSession;
use pocketmine\event\Event;

class AnticheatEvent extends Event{

	public function __construct(private readonly AnticheatSession $session){}

	public function getSession() : AnticheatSession{
		return $this->session;
	}
}
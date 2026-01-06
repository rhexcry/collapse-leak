<?php

declare(strict_types=1);

namespace collapse\system\party\event;

use collapse\system\party\Party;
use pocketmine\event\Event;

class PartyEvent extends Event{

	public function __construct(
		protected ?Party $party
	){}

	public function getParty() : Party{
		return $this->party;
	}
}
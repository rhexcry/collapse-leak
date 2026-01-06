<?php

declare(strict_types=1);

namespace collapse\system\party\event;

use collapse\player\CollapsePlayer;
use collapse\system\party\Party;

final class PartyPlayerLeftEvent extends PartyEvent{

	public function __construct(?Party $party, private readonly CollapsePlayer $player){
		parent::__construct($party);
	}

	public function getPlayer() : CollapsePlayer{
		return $this->player;
	}
}
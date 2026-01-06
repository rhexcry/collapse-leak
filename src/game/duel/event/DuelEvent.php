<?php

declare(strict_types=1);

namespace collapse\game\duel\event;

use collapse\game\duel\Duel;
use pocketmine\event\Event;

abstract class DuelEvent extends Event{

	public function __construct(
		protected readonly Duel $duel
	){}

	public function getDuel() : Duel{
		return $this->duel;
	}
}

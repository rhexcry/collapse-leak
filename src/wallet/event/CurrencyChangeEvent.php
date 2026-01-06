<?php

declare(strict_types=1);

namespace collapse\wallet\event;

use collapse\player\profile\Profile;
use collapse\wallet\currency\Currency;
use pocketmine\event\Event;

abstract class CurrencyChangeEvent extends Event{

	public function __construct(
		protected readonly Profile $profile,
		protected readonly Currency $currency,
		protected readonly int $amount
	){}

	public function getProfile() : Profile{
		return $this->profile;
	}

	public function getCurrency() : Currency{
		return $this->currency;
	}

	public function getAmount() : int{
		return $this->amount;
	}
}

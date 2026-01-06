<?php

declare(strict_types=1);

namespace collapse\wallet;

use collapse\player\profile\Profile;
use collapse\wallet\currency\Currency;
use collapse\wallet\event\CurrencyAddEvent;
use collapse\wallet\event\CurrencyReduceEvent;
use collapse\wallet\event\CurrencySetEvent;

final class Wallet{

	private static function changeAmount(Currency $currency, Profile $profile, int $amount) : void{
		$profile->setCurrencyAmount($currency, $amount);
		if($profile->getPlayer() === null){
			$profile->save();
		}
	}

	public static function reduce(Currency $currency, Profile $profile, int $amount) : void{
		(new CurrencyReduceEvent($profile, $currency, $amount))->call();
		self::changeAmount($currency, $profile, $profile->getCurrencyAmount($currency) - $amount);
	}

	public static function add(Currency $currency, Profile $profile, int $amount) : void{
		(new CurrencyAddEvent($profile, $currency, $amount))->call();
		self::changeAmount($currency, $profile, $profile->getCurrencyAmount($currency) + $amount);
	}

	public static function set(Currency $currency, Profile $profile, int $amount) : void{
		(new CurrencySetEvent($profile, $currency, $amount))->call();
		self::changeAmount($currency, $profile, $amount);
	}

	public static function get(Currency $currency, Profile $profile) : int{
		return $profile->getCurrencyAmount($currency);
	}
}

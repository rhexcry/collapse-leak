<?php

declare(strict_types=1);

namespace collapse\wallet\currency;

use collapse\player\profile\Profile;
use collapse\wallet\Wallet;
use function intval;

final class StarCurrency implements Currency{
	public const int DUST_EXCHANGE_RATE = 10;

	public function getName() : string{
		return 'StarCoins';
	}

	public function getDisplayName() : string{
		return 'SC';
	}

	public function getDefaultValue() : int{
		return 0;
	}

	public static function exchangeFromDust(Profile $profile, ?int &$result = null, ?int &$source = null, ?int $count = null) : ExchangeResult{
		//TODO: improve this
		$dust = Wallet::get(Currencies::DUST(), $profile);
		if($dust < ($count ?? 1)){
			return ExchangeResult::NotEnoughSource;
		}
		$dust = $count ?? $dust;
		$result = intval($dust / self::DUST_EXCHANGE_RATE);
		$source = intval($result * self::DUST_EXCHANGE_RATE);
		if($result < 1){
			return ExchangeResult::NotEnoughSource;
		}
		Wallet::reduce(Currencies::DUST(), $profile, $source);
		Wallet::add(Currencies::STAR(), $profile, $result);
		return ExchangeResult::Success;
	}
}

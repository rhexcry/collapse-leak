<?php

declare(strict_types=1);

namespace collapse\wallet\currency;

final class DustCurrency implements Currency{

	public function getName() : string{
		return 'DustCoins';
	}

	public function getDisplayName() : string{
		return 'DC';
	}

	public function getDefaultValue() : int{
		return 0;
	}
}

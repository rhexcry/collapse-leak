<?php

declare(strict_types=1);

namespace collapse\wallet\currency;

enum ExchangeResult{

	case NotEnoughSource;
	case Success;
}

<?php

declare(strict_types=1);

namespace collapse\feature\reward;

use collapse\feature\reward\concrete\ValuteReward;
use InvalidArgumentException;

final class RewardFactory{

	public static function create(RewardConfig $config) : Reward{
		return match($config->getType()){
			RewardType::Valute => new ValuteReward(0),
			default => throw new InvalidArgumentException("Unknown reward type")
		};
	}
}

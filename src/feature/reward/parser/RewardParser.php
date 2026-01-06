<?php

declare(strict_types=1);

namespace collapse\feature\reward\parser;

use collapse\feature\reward\Reward;
use collapse\feature\reward\RewardConfig;
use collapse\feature\reward\RewardFactory;
use collapse\feature\reward\RewardType;

final class RewardParser{

	/**
	 * @return Reward[]
	 */
	public static function parse(array $data) : array{
		$result = [];

		foreach($data as $rewardData){
			$rewardType = RewardType::tryFrom($rewardData['type']);

			if($rewardType === null){
				throw new \RuntimeException('Bad config. Undefined reward type: ' . $rewardData['type']);
			}

			$result[] = RewardFactory::create(new RewardConfig(
				$rewardType,
				$rewardData['value'] ?? null,
			));

		}

		return $result;
	}
}

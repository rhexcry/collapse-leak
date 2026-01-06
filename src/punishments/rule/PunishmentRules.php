<?php

declare(strict_types=1);

namespace collapse\punishments\rule;

use pocketmine\utils\Config;
use function array_reduce;
use function substr;

final class PunishmentRules{
	/** @var Rule[] */
	private static array $rules = [];

	public static function init(string $configPath) : void{
		$config = new Config($configPath, Config::JSON);

		self::$rules = array_reduce(
			array_keys($config->getAll()),
			function(array $carry, string $ruleId) use ($config){
				$data = $config->get($ruleId);
				$carry[$ruleId] = new Rule(
					$ruleId,
					$data['description'],
					$data['cropped_description'] ?? null,
					self::parseDuration($data['duration'] ?? null),
					$data['parameters'] ?? []
				);
				return $carry;
			},
			[]
		);
	}

	public static function getRule(string $code) : ?Rule{
		return self::$rules[$code] ?? null;
	}

	public static function getAllRules() : array{
		return self::$rules;
	}

	private static function parseDuration(?string $duration) : ?int{
		if($duration === null || $duration === 'never'){
			return null;
		}

		$value = (int) substr($duration, 0, -1);
		$unit = substr($duration, -1);

		return match ($unit) {
			'h' => $value * 3600,
			'd' => $value * 86400,
			'w' => $value * 604800,
			'm' => $value * 2592000,
			'y' => $value * 31536000,
			default => 86400
		};
	}
}

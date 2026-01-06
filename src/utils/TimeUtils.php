<?php

declare(strict_types=1);

namespace collapse\utils;

use collapse\i18n\CollapseTranslationFactory;
use collapse\i18n\Translator;
use pocketmine\lang\Translatable;
use function array_combine;
use function array_keys;
use function array_map;
use function implode;

final readonly class TimeUtils{

	private static function createConvertedResult(float $time, bool $short, Translatable $shortKey, Translatable $casesKey) : Translatable{
		return new Translatable($time . ($short ? '{%0}' : ' {%' . Translator::SIGNAL_TEXT_CONVERT . 'format}'), $short ? [$shortKey] : [Translator::SIGNAL_TEXT_CONVERT . 'format' => new Translatable('', [$time, $casesKey])]);
	}

	public static function convert(int $time, bool $short = false) : Translatable{
		$remaining = $time;
		$components = [];

		foreach([
			[
				'divisor' => 86400,
				'short' => CollapseTranslationFactory::time_day_short(),
				'cases' => CollapseTranslationFactory::time_day_cases(),
			],
			[
				'divisor' => 3600,
				'short' => CollapseTranslationFactory::time_hour_short(),
				'cases' => CollapseTranslationFactory::time_hour_cases(),
			],
			[
				'divisor' => 60,
				'short' => CollapseTranslationFactory::time_minute_short(),
				'cases' => CollapseTranslationFactory::time_minute_cases(),
			],
			[
				'divisor' => 1,
				'short' => CollapseTranslationFactory::time_second_short(),
				'cases' => CollapseTranslationFactory::time_second_cases(),
			],
		] as $unit){
			$value = (int) ($remaining / $unit['divisor']);
			$remaining %= $unit['divisor'];

			if($value > 0){
				$components[] = self::createConvertedResult(
					$value,
					$short,
					$unit['short'],
					$unit['cases']
				);
			}
		}

		if($components === [] && $time === 0){
			$components[] = self::createConvertedResult(
				0,
				$short,
				CollapseTranslationFactory::time_second_short(),
				CollapseTranslationFactory::time_second_cases()
			);
		}

		$placeholders = array_map(
			fn(int $i) : string => '{%' . $i . '}',
			array_keys($components)
		);

		return new Translatable(
			implode(' ', $placeholders),
			array_combine(array_keys($components), $components)
		);
	}
}

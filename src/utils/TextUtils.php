<?php

declare(strict_types=1);

namespace collapse\utils;

use collapse\player\rank\Rank;
use pocketmine\utils\TextFormat;
use function array_keys;
use function array_map;
use function explode;
use function implode;
use function max;
use function mb_strlen;
use function min;
use function str_repeat;

final readonly class TextUtils{

	private const array ROMAN_NUMERALS = [
		'M' => 1000,
		'CM' => 900,
		'D' => 500,
		'CD' => 400,
		'C' => 100,
		'XC' => 90,
		'L' => 50,
		'XL' => 40,
		'X' => 10,
		'IX' => 9,
		'V' => 5,
		'IV' => 4,
		'I' => 1
	];

	private function __construct(){
	}

	public static function convert(float $number, string $values) : string{
		$number = (int) $number;
		$values = explode(';', $values);
		$cases = [2, 0, 1, 1, 1, 2];
		return $values[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)] ?? ''] ?? '';
	}

	public static function numberToRoman(int $number) : string{
		$result = '';
		foreach(self::ROMAN_NUMERALS as $roman => $n){
			$result .= str_repeat($roman, (int) ($number / $n));
			$number %= $n;
		}
		return $result;
	}

	public static function align(string $text) : string{
		$cleanText = TextFormat::clean($text);
		$cleanLines = explode("\n", $cleanText);
		$lines = explode("\n", $text);

		$maxWidth = max(array_map(mb_strlen(...), $cleanLines));

		return implode("\n", array_map(
			static fn(int $i) : string => str_repeat(
					" ",
					(int) (($maxWidth - mb_strlen($cleanLines[$i])) / 2)
				) . $lines[$i],
			array_keys($lines)
		));
	}

	public static function getNameWithFontedRank(Rank $rank, string $name) : string{
		return $rank->toFont() . ' ' . $name;
	}
}

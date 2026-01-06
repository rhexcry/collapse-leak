<?php

declare(strict_types=1);

namespace collapse\censor;

use function array_unique;
use function mb_stripos;
use function mb_strlen;
use function preg_match_all;
use function preg_replace;
use function str_repeat;
use function strtr;

final class Censor{

	private const array CHARACTER_MAP = [
		'P' => 'пПnPp',
		'I' => 'иИiI1u',
		'E' => 'еЕeE',
		'D' => 'дДdD',
		'Z' => 'зЗ3zZ3',
		'M' => 'мМmM',
		'U' => 'уУyYuU',
		'O' => 'оОoO0',
		'L' => 'лЛlL',
		'S' => 'сСcCsS',
		'A' => 'аАaA@',
		'N' => 'нНhH',
		'G' => 'гГgG',
		'CH' => 'чЧ4',
		'K' => 'кКkK',
		'C' => 'цЦcC',
		'R' => 'рРpPrR',
		'H' => 'хХxXhH',
		'YI' => 'йЙy',
		'YA' => 'яЯ',
		'YO' => 'ёЁ',
		'YU' => 'юЮ',
		'B' => 'бБ6bB',
		'T' => 'тТtT',
		'HS' => 'ъЪ',
		'SS' => 'ьЬ',
		'Y' => 'ыЫ',
		'SH' => 'шшЩЩ',
		'V' => 'вВvVBb',
	];

	private const array EXCEPTIONS = [
		'команд', 'комманд', 'рубл', 'премь', 'оскорб', 'краснояр', 'бояр', 'ноябр', 'карьер', 'мандат',
		'употр', 'плох', 'интер', 'веер', 'фаер', 'феер', 'hyundai', 'тату', 'браконь',
		'roup', 'сараф', 'держ', 'слаб', 'ридер', 'истреб', 'потреб', 'коридор', 'sound', 'дерг',
		'подоб', 'коррид', 'дубл', 'курьер', 'экст', 'try', 'enter', 'oun', 'aube', 'ibarg', '16',
		'kres', 'глуб', 'ebay', 'eeb', 'shuy', 'ансам', 'cayenne', 'ain', 'oin', 'тряс', 'ubu', 'uen',
		'uip', 'oup', 'кораб', 'боеп', 'деепр', 'хульс', 'een', 'ee6', 'ein', 'сугуб', 'карб', 'гроб',
		'лить', 'рсук', 'влюб', 'хулио', 'ляп', 'граб', 'ибог', 'вело', 'ебэ', 'перв', 'eep', 'ying',
		'laun', 'чаепитие', 'oub', 'мандарин', 'гондольер', 'гоша', 'фраг', 'гав', 'говор', 'гавор',
		'помога', 'памага', 'гов', 'огонь', 'o1b2', 'ведро', 'догон', 'ав'
	];

	public function __construct(
		private readonly PatternBuilder $patternBuilder = new PatternBuilder(),
		private readonly string $replacement = '*'
	){}

	public function filter(string $text) : string{
		$pattern = $this->patternBuilder->build(self::CHARACTER_MAP, self::EXCEPTIONS);
		preg_match_all($pattern, $text, $matches);

		return empty($matches[0]) ? $text : $this->replaceMatches($text, $matches[0]);
	}

	public function containsBadWords(string $text) : bool{
		return $text !== $this->filter($text);
	}

	private function replaceMatches(string $text, array $matches) : string{
		$replacements = [];
		foreach(array_unique($matches) as $match){
			$cleanWord = preg_replace('/[^\p{L}]/u', '', $match);
			if($this->isException($cleanWord)){
				continue;
			}

			$replacements[$match] = str_repeat($this->replacement, mb_strlen($cleanWord));
		}

		return strtr($text, $replacements);
	}

	private function isException(string $word) : bool{
		foreach(self::EXCEPTIONS as $exception){
			if(mb_stripos($word, $exception) !== false){
				return true;
			}
		}
		return false;
	}
}

<?php

declare(strict_types=1);

namespace collapse\resourcepack;

use pocketmine\utils\TextFormat;

final class Font{

	public const string SCOREBOARD_LINE = "\u{E702}";
	public const string CHAR_SEPARATOR = "\u{E328}";
	public const string CHAR_DOT = "\u{E32B}";
	public const string CHAR_PERCENT = "\u{E3A8}";

	public const string RANK_MODERATOR = "\u{E222}";
	public const string RANK_ADMIN = "\u{E223}";
	public const string RANK_BLAZING = "\u{E224}";
	public const string RANK_ETHEREUM = "\u{E225}";
	public const string RANK_FAMOUS = "\u{E226}";
	public const string RANK_LUMINOUS = "\u{E227}";
	public const string RANK_MEDIA = "\u{E228}";
	public const string RANK_OWNER = "\u{E229}";
	public const string RANK_NECESSARY = "\u{E22A}";
	public const string RANK_YONKO = "\u{E22B}";
	public const string RANK_ARCANE = "\u{E22C}";
	public const string RANK_CELESTIAL = "\u{E22D}";

	private const array CHAR_SETS = [
		'default' => [
			'a-z' => "\u{E3C0}",
			'A-Z' => "\u{E380}",
			'0-9' => "\u{E39A}",
			'!' => "\u{E3A4}",
			':' => "\u{E3B9}",
			'-' => "\u{E3AE}",
			'а-я' => "\u{E400}",
			'А-Я' => "\u{E442}",
		],
		'bold' => [
			'a-z' => "\u{E300}",
			'A-Z' => "\u{E300}",
			'0-9' => "\u{E31A}",
			'!' => "\u{E354}",
			':' => "\u{E329}",
			'-' => "\u{E32E}",
			'а-я' => "\u{E421}",
			'А-Я' => "\u{E421}",
		],
		'white' => [
			'a-z' => "\u{E5C0}",
			'A-Z' => "\u{E580}",
			'0-9' => "\u{E59A}",
			'!' => "\u{E5A4}",
			':' => "\u{E5B9}",
			'-' => "\u{E5AE}",
			'а-я' => "\u{E600}",
			'А-Я' => "\u{E600}",
		],
		'white_bold' => [
			'a-z' => "\u{E500}",
			'A-Z' => "\u{E500}",
			'0-9' => "\u{E51A}",
			'!' => "\u{E554}",
			':' => "\u{E529}",
			'-' => "\u{E52E}",
			'а-я' => "\u{E621}",
			'А-Я' => "\u{E621}",
		],
		'dark_aqua' => [
			'a-z' => "\u{E960}",
			'A-Z' => "\u{E930}",
			'0-9' => "\u{E97A}",
		],
		'red' => [
			'a-z' => "\u{EA60}",
			'A-Z' => "\u{EA30}",
			'0-9' => "\u{EA7A}",
		],
		'gold' => [
			'a-z' => "\u{EB60}",
			'A-Z' => "\u{EB30}",
			'0-9' => "\u{EB7A}",
		],
		'light_purple' => [
			'a-z' => "\u{EC60}",
			'A-Z' => "\u{EC30}",
			'0-9' => "\u{EC7A}",
		],
		'dark_purple' => [
			'a-z' => "\u{ED60}",
			'A-Z' => "\u{ED30}",
			'0-9' => "\u{ED7A}",
		],
		'amethyst' => [
			'a-z' => "\u{EE60}",
			'A-Z' => "\u{EE30}",
			'0-9' => "\u{EE7A}",
		],
		'blue' => [
			'a-z' => "\u{EF60}",
			'A-Z' => "\u{EF30}",
			'0-9' => "\u{EF7A}",
		],
		'dark_blue' => [
			'a-z' => "\u{F060}",
			'A-Z' => "\u{F030}",
			'0-9' => "\u{F07A}",
		],
		'aqua' => [
			'a-z' => "\u{F160}",
			'A-Z' => "\u{F130}",
			'0-9' => "\u{F17A}",
		],
		'green' => [
			'a-z' => "\u{F260}",
			'A-Z' => "\u{F230}",
			'0-9' => "\u{F27A}",
		],
		'redstone' => [
			'a-z' => "\u{F360}",
			'A-Z' => "\u{F330}",
			'0-9' => "\u{F37A}",
		]
	];

	private const array CHAR_MAPPING = [
		'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm',
		'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
		'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
		'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
		'1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '!', ':', '|', '.', '%', '-',
		'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м',
		'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ',
		'ы', 'ь', 'э', 'ю', 'я',
		'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М',
		'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ',
		'Ы', 'Ь', 'Э', 'Ю', 'Я'
	];

	private static function generateReplacements(string $style) : array{
		$set = self::CHAR_SETS[$style] ?? self::CHAR_SETS['default'];
		$replacements = [];

		if(isset($set['a-z'])){
			$start = ord('a');
			$charCode = hexdec(substr(json_encode($set['a-z']), 3, 4));
			for($i = 0; $i < 26; $i++){
				$replacements[chr($start + $i)] = self::unicodeChar($charCode + $i);
			}
		}

		if(isset($set['A-Z'])){
			if($set['A-Z'] === ($set['a-z'] ?? null)){
				for($i = 0; $i < 26; $i++){
					$replacements[chr(ord('A') + $i)] = $replacements[chr(ord('a') + $i)] ?? chr(ord('A') + $i);
				}
			}else{
				$charCode = hexdec(substr(json_encode($set['A-Z']), 3, 4));
				for($i = 0; $i < 26; $i++){
					$replacements[chr(ord('A') + $i)] = self::unicodeChar($charCode + $i);
				}
			}
		}

		if(isset($set['0-9'])){
			$charCode = hexdec(substr(json_encode($set['0-9']), 3, 4));
			$digitsOrder = [1, 2, 3, 4, 5, 6, 7, 8, 9, 0];
			foreach($digitsOrder as $index => $digit){
				$replacements[(string) $digit] = self::unicodeChar($charCode + $index);
			}
		}

		$replacements['!'] = $set['!'] ?? '!';
		$replacements[':'] = $set[':'] ?? ':';
		$replacements['-'] = $set['-'] ?? '-';

		if(isset($set['а-я'])){
			$russianLower = 'абвгдеёжзийклмнопрстуфхцчшщъыьэюя';
			$charCode = hexdec(substr(json_encode($set['а-я']), 3, 4));
			for($i = 0; $i < mb_strlen($russianLower); $i++){
				$replacements[mb_substr($russianLower, $i, 1)] = self::unicodeChar($charCode + $i);
			}
		}

		if(isset($set['А-Я'])){
			$russianUpper = 'АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ';
			if($set['А-Я'] === ($set['а-я'] ?? null)){
				for($i = 0; $i < mb_strlen($russianUpper); $i++){
					$replacements[mb_substr($russianUpper, $i, 1)] = $replacements[mb_substr($russianLower, $i, 1)] ?? mb_substr($russianUpper, $i, 1);
				}
			}else{
				$charCode = hexdec(substr(json_encode($set['А-Я']), 3, 4));
				for($i = 0; $i < mb_strlen($russianUpper); $i++){
					$replacements[mb_substr($russianUpper, $i, 1)] = self::unicodeChar($charCode + $i);
				}
			}
		}

		$replacements['|'] = self::CHAR_SEPARATOR;
		$replacements['.'] = self::CHAR_DOT;
		$replacements['%'] = self::CHAR_PERCENT;

		return $replacements;
	}

	private static function unicodeChar(int $code) : string{
		return json_decode('"' . sprintf('\\u%04X', $code) . '"');
	}

	private static array $replaceCache = [];

	private static function getReplacements(string $style) : array{
		if(!isset(self::$replaceCache[$style])){
			self::$replaceCache[$style] = self::generateReplacements($style);
		}

		return self::$replaceCache[$style];
	}

	public static function bold(string $text) : string{
		return strtr($text, self::getReplacements('bold'));
	}

	public static function whiteBold(string $text) : string{
		return strtr($text, self::getReplacements('white_bold'));
	}

	public static function text(string $text) : string{
		return strtr($text, self::getReplacements('default'));
	}

	public static function white(string $text) : string{
		return strtr($text, self::getReplacements('white'));
	}

	public static function darkAqua(string $text) : string{
		return strtr($text, self::getReplacements('dark_aqua'));
	}

	public static function gold(string $text) : string{
		return strtr($text, self::getReplacements('gold'));
	}

	public static function lightPurple(string $text) : string{
		return strtr($text, self::getReplacements('light_purple'));
	}

	public static function darkPurple(string $text) : string{
		return strtr($text, self::getReplacements('dark_purple'));
	}

	public static function amethyst(string $text) : string{
		return strtr($text, self::getReplacements('amethyst'));
	}

	public static function darkBlue(string $text) : string{
		return strtr($text, self::getReplacements('dark_blue'));
	}

	public static function aqua(string $text) : string{
		return strtr($text, self::getReplacements('aqua'));
	}

	public static function redstone(string $text) : string{
		return strtr($text, self::getReplacements('redstone'));
	}

	public static function red(string $text) : string{
		return strtr($text, self::getReplacements('red'));
	}

	public static function green(string $text) : string{
		return strtr($text, self::getReplacements('green'));
	}

	public static function blue(string $text) : string{
		return strtr($text, self::getReplacements('blue'));
	}

	public static function minecraftColorToUnicodeFont(string $text) : string{
		$text = TextFormat::colorize($text, '&');

		$result = "";
		$currentFormat = null;
		$pendingFormatCode = null; // Для хранения необработанных кодов форматов

		$pattern = '/(' . preg_quote(TextFormat::ESCAPE, '/') . '[0-9a-z])/u';
		$tokenPattern = '/^' . preg_quote(TextFormat::ESCAPE, '/') . '([0-9a-z])$/u';

		$tokens = preg_split($pattern, $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		if($tokens === false){
			return $text;
		}

		$formatMapping = [
			TextFormat::MINECOIN_GOLD => 'gold',
			TextFormat::GREEN => 'green',
			TextFormat::GOLD => 'gold',
			TextFormat::BLUE => 'blue',
			TextFormat::DARK_PURPLE => 'dark_purple',
			TextFormat::RED => 'red',
			TextFormat::LIGHT_PURPLE => 'light_purple',
			TextFormat::DARK_BLUE => 'dark_blue',
			TextFormat::MATERIAL_AMETHYST => 'amethyst',
			TextFormat::AQUA => 'aqua',
			TextFormat::DARK_AQUA => 'dark_aqua',
			TextFormat::MATERIAL_REDSTONE => 'redstone',
			TextFormat::WHITE => 'white',
		];

		$supportedFormats = array_keys($formatMapping);

		foreach($tokens as $token){
			if(preg_match($tokenPattern, $token, $matches)){
				$code = $matches[1];

				if($code === 'r'){
					// Сброс формата - добавляем накопленные коды и сбрасываем
					if($pendingFormatCode !== null){
						$result .= $pendingFormatCode;
						$pendingFormatCode = null;
					}
					$currentFormat = null;
					continue;
				}

				$colorCode = TextFormat::ESCAPE . $code;
				if(in_array($colorCode, $supportedFormats, true)){
					// Найден поддерживаемый формат - добавляем накопленные коды
					if($pendingFormatCode !== null){
						$result .= $pendingFormatCode;
						$pendingFormatCode = null;
					}
					$currentFormat = $formatMapping[$colorCode];
				}else{
					// Неподдерживаемый формат - накапливаем код
					if($pendingFormatCode === null){
						$pendingFormatCode = $colorCode;
					}else{
						$pendingFormatCode .= $colorCode;
					}
					$currentFormat = null;
				}
			}else{
				// Обработка текста
				if($currentFormat !== null && isset(self::CHAR_SETS[$currentFormat])){
					// Преобразуем текст с поддержкой Unicode шрифта
					$result .= strtr($token, self::getReplacements($currentFormat));
				}else{
					// Добавляем накопленные коды форматов (если есть) и текст
					if($pendingFormatCode !== null){
						$result .= $pendingFormatCode;
						$pendingFormatCode = null;
					}
					$result .= $token;
				}
			}
		}

		// Добавляем оставшиеся накопленные коды в конце
		if($pendingFormatCode !== null){
			$result .= $pendingFormatCode;
		}

		return $result;
	}

}
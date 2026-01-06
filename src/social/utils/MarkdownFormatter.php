<?php

declare(strict_types=1);

namespace collapse\social\utils;

use function str_repeat;
use function str_replace;
use const EOL;

final readonly class MarkdownFormatter{

	public static function textToBold(string $text) : string{
		return '*' . $text . '*';
	}

	public static function textToItalic(string $text) : string{
		return '_' . $text . '_';
	}

	public static function textToStrikethrough(string $text) : string{
		return '~~' . $text . '~~';
	}

	public static function textToMonospace(string $text) : string{
		return '`' . $text . '`';
	}

	public static function textToLink(string $text, string $url) : string{
		return '[' . $text . '](' . $url . ')';
	}

	public static function textToHeader(string $text, int $level = 1) : string{
		if($level < 1 || $level > 6){
			throw new \InvalidArgumentException('Level range must be between 1 and 6.');
		}
		return str_repeat('#', $level) . ' ' . $text;
	}

	public static function textToCodeBlock(string $text, string $language = '') : string{
		return '```' . $language . EOL . $text . EOL . '```';
	}

	public static function toEscape(string $str) : string{
		return str_replace(
			['-', '~', '`', '.', '(', ')'],
			['\-', '\~', '\`', '\.', '\(', '\)'],
			$str
		);
	}
}

<?php

declare(strict_types=1);

namespace collapse\i18n;

use collapse\i18n\types\LanguageInterface;
use collapse\utils\TextUtils;
use pocketmine\lang\Translatable;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Utils;
use Symfony\Component\Filesystem\Path;
use function array_map;
use function is_string;
use function parse_ini_file;
use function str_replace;
use const INI_SCANNER_RAW;

final class Translator{

	public const string DATA_PATH_PREFIX = 'i18n';

	public const string SIGNAL_TEXT_CONVERT = '^';

	private array $translations;
	private LanguageInterface $currentLanguage;

	public function __construct(
		private readonly string $dataPath
	){}

	public function setLanguage(LanguageInterface $language) : self{
		$this->currentLanguage = $language;
		$translationFile = Path::join($this->dataPath, $language->getCode() . '.ini');

		$this->translations = array_map('stripcslashes', Utils::assumeNotFalse(parse_ini_file($translationFile, false, INI_SCANNER_RAW), 'Translation file for ' . $language->getCode() . ' not found.'));

		return $this;
	}

	public function translate(Translatable $translation) : string{
		$message = $this->translations[$translation->getText()] ?? $translation->getText();

		foreach($translation->getParameters() as $placeholder => $value){
			$message = str_replace('{%' . $placeholder . '}', match(true){
				is_string($placeholder) && $placeholder[0] === self::SIGNAL_TEXT_CONVERT && $value instanceof Translatable => TextUtils::convert((float) $value->getParameters()[0], $this->translate($value->getParameters()[1])),
				$value instanceof Translatable => $this->translate($value),
				default => (string) $value
			}, $message);
		}

		return TextFormat::colorize($message);
	}

	public function getCurrentLanguage() : LanguageInterface{
		return $this->currentLanguage;
	}
}

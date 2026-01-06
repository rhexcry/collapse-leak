<?php

declare(strict_types=1);

namespace collapse\i18n\types;

use collapse\Practice;
use pocketmine\utils\CloningRegistryTrait;
use function mb_strtoupper;

/**
 * @method static LanguageInterface ENGLISH()
 * @method static LanguageInterface RUSSIAN()
 */
final class Language{
	use CloningRegistryTrait;

	protected static function setup() : void{
		self::register('english', new DynamicLanguage('en_US', 'English'));
		self::register('russian', new DynamicLanguage('ru_RU', 'Russian'));
	}

	public static function register(string $memberName, LanguageInterface $language) : void{
		self::_registryRegister($memberName, $language);
		Practice::getInstance()->getLogger()->info('Registered new language: ' . $language->getName());
	}

	public static function fromString(string $name) : ?LanguageInterface{
		self::checkInit();
		$upperName = mb_strtoupper($name);
		if(!isset(self::$members[$upperName])){
			return null;
		}

		/** @var LanguageInterface $member */
		$member = self::preprocessMember(self::$members[$upperName]);
		return $member;
	}

	/**
	 * @return LanguageInterface[]
	 */
	public static function all() : array{
		return self::_registryGetAll();
	}
}

<?php

declare(strict_types=1);

namespace collapse\i18n;

use collapse\i18n\command\LanguageCommand;
use collapse\i18n\event\ProfileChangeLanguageEvent;
use collapse\i18n\types\Language;
use collapse\i18n\types\LanguageInterface;
use collapse\player\CollapsePlayer;
use collapse\player\profile\Profile;
use collapse\Practice;
use pocketmine\lang\Translatable;
use Symfony\Component\Filesystem\Path;

final class TranslatorManager{

	/** @var Translator[] */
	private array $translators = [];

	private Translator $defaultTranslator;

	public function __construct(private readonly Practice $plugin){
		foreach(Language::all() as $language){
			$this->plugin->saveResource(Path::join(Translator::DATA_PATH_PREFIX, $language->getCode() . '.ini'), true);
			$translator = new Translator(Path::join($this->plugin->getDataFolder(), Translator::DATA_PATH_PREFIX));
			$translator->setLanguage($language);
			$this->registerLanguage($language->getCode(), $translator);
		}
		$this->defaultTranslator = $this->translators[TranslatorLocales::ENGLISH];

		$this->plugin->getServer()->getPluginManager()->registerEvents(new TranslatorListener($this), $this->plugin);
		$this->plugin->getServer()->getCommandMap()->register('collapse', new LanguageCommand($this));
	}

	public function getDefaultTranslator() : Translator{
		return $this->defaultTranslator;
	}

	private function registerLanguage(string $locale, Translator $translator) : void{
		$this->translators[$locale] = $translator;
	}

	public function fromLocale(string $locale) : Translator{
		return $this->translators[$locale] ?? $this->defaultTranslator;
	}

	public function setProfileLanguage(Profile $profile, LanguageInterface $language) : void{
		$profile->setLanguage($language);
		$profile->setTranslator($this->fromLocale($language->getCode()));
		(new ProfileChangeLanguageEvent($profile, $language))->call();
	}

	/**
	 * @param CollapsePlayer[]|null $recipients
	 */
	public function broadcastTranslatedMessage(Translatable $translation, ?array $recipients = null, bool $prefix = true) : void{
		foreach($recipients ?? Practice::onlinePlayers() as $recipient){
			$recipient->sendTranslatedMessage($translation, $prefix);
		}
		$this->plugin->getServer()->getLogger()->info($this->defaultTranslator->translate($translation));
	}
}

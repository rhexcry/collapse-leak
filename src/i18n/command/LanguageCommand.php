<?php

declare(strict_types=1);

namespace collapse\i18n\command;

use collapse\command\attribute\OnlyForPlayerCommand;
use collapse\command\CollapseCommand;
use collapse\command\CommandArguments;
use collapse\i18n\CollapseTranslationFactory;
use collapse\i18n\TranslatorManager;
use collapse\i18n\types\Language;
use collapse\i18n\types\LanguageInterface;
use collapse\player\CollapsePlayer;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use function array_map;
use function array_values;
use function implode;

#[OnlyForPlayerCommand]
class LanguageCommand extends CollapseCommand{

	public function __construct(private readonly TranslatorManager $translatorManager){
		parent::__construct('language', 'Change a language');
		$this->setPermission('collapse.command.language');
		$this->commandArguments = new CommandArguments();
		$this->commandArguments->addEnum(0, 'lang', array_values(array_map(static function(LanguageInterface $language) : string{
			return $language->getName();
		}, Language::all())));
	}

	/**
	 * @param CollapsePlayer $sender
	 */
	protected function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(!isset($args[0])){
			$sender->sendTranslatedMessage(CollapseTranslationFactory::command_language_usage());
			return;
		}

		$language = Language::fromString($args[0]);
		if($language === null){
			$sender->sendTranslatedMessage(CollapseTranslationFactory::command_language_language_not_found(implode(', ', array_map(static function(LanguageInterface $language) : string{
				return $language->getName();
			}, Language::all()))));
			return;
		}

		if($sender->getProfile()->getTranslator()->getCurrentLanguage()->getName() === $language->getName()){
			$sender->sendTranslatedMessage(CollapseTranslationFactory::command_language_already());
			return;
		}

		$this->translatorManager->setProfileLanguage($sender->getProfile(), $language);
		$sender->sendTranslatedMessage(CollapseTranslationFactory::command_language_successfully($language->getName()));
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_language_description();
	}
}

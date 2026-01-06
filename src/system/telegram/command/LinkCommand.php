<?php

declare(strict_types=1);

namespace collapse\system\telegram\command;

use collapse\command\attribute\OnlyForPlayerCommand;
use collapse\command\CollapseCommand;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\system\telegram\TelegramManager;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;

#[OnlyForPlayerCommand]
final class LinkCommand extends CollapseCommand {

	public function __construct(private readonly TelegramManager $manager){
		parent::__construct('link', 'Generate telegram link code');
		$this->setPermission('collapse.command.link');
	}

	protected function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		/** @var CollapsePlayer $sender */
		$code = $this->manager->getCode($sender);
		if($code !== null){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_link_already_exist($code));
			return;
		}

		$code = $this->manager->generateCode($sender);
		$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_link_success($code));
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_link_description();
	}
}
<?php

declare(strict_types=1);

namespace collapse\feature\concrete\quest\command;

use collapse\command\attribute\OnlyForPlayerCommand;
use collapse\command\CollapseCommand;
use collapse\feature\concrete\quest\form\AvailableQuestsForm;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;

#[OnlyForPlayerCommand]
final class QuestsCommand extends CollapseCommand{

	public function __construct(){
		parent::__construct('quests', 'Quests');
		$this->setPermission('collapse.command.quests');
	}

	/**
	 * @param CollapsePlayer $sender
	 */
	protected function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		$sender->sendForm(new AvailableQuestsForm($sender));
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_quest_description();
	}
}

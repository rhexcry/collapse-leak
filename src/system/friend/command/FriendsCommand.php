<?php

declare(strict_types=1);

namespace collapse\system\friend\command;

use collapse\command\attribute\OnlyForPlayerCommand;
use collapse\command\CollapseCommand;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\system\friend\form\FriendsForm;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;

#[OnlyForPlayerCommand]
final class FriendsCommand extends CollapseCommand{

	public function __construct(){
		parent::__construct('friends', 'Friends management');
		$this->setPermission('collapse.command.friends');
	}

	/**
	 * @param CollapsePlayer $sender
	 */
	protected function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(!Practice::getInstance()->getLobbyManager()->isInLobby($sender)){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::only_in_lobby());
			return;
		}

		$sender->sendForm(new FriendsForm($sender));
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_friends_description();
	}
}

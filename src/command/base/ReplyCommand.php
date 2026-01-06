<?php

declare(strict_types=1);

namespace collapse\command\base;

use collapse\command\attribute\OnlyForPlayerCommand;
use collapse\command\CollapseCommand;
use collapse\command\CommandArguments;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\player\pm\PrivateMessages;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use function implode;

#[OnlyForPlayerCommand]
final class ReplyCommand extends CollapseCommand{

	public function __construct(){
		parent::__construct('reply', 'Reply to a player\'s private message', '/reply <msg>', ['r']);
		$this->setPermission('collapse.command.reply');
		$this->commandArguments = new CommandArguments();
		$this->commandArguments->addParameter(0, 'msg');
	}

	/**
	 * @param CollapsePlayer $sender
	 */
	protected function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(!isset($args[0])){
			$sender->sendTranslatedMessage(CollapseTranslationFactory::command_reply_usage());
			return;
		}

		if(!PrivateMessages::isAnyoneSentFor($sender)){
			$sender->sendTranslatedMessage(CollapseTranslationFactory::command_reply_nobody());
			return;
		}

		$player = PrivateMessages::getPlayerForReply($sender);
		if($player === null){
			$sender->sendTranslatedMessage(CollapseTranslationFactory::player_not_found());
			return;
		}

		PrivateMessages::send($sender, $player, implode(' ', $args));
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_reply_description();
	}
}

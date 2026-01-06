<?php

declare(strict_types=1);

namespace collapse\command\base;

use collapse\command\CollapseCommand;
use collapse\command\CommandArguments;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\player\pm\PrivateMessages;
use collapse\Practice;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use function array_shift;
use function count;
use function implode;

final class TellCommand extends CollapseCommand{

	public function __construct(){
		parent::__construct('tell', 'Send a private message', '/tell <player> <msg>', ['whisper', 'pm']);
		$this->setPermission('collapse.command.tell');
		$this->commandArguments = new CommandArguments();
		$this->commandArguments->addParameter(0, 'player', AvailableCommandsPacket::ARG_TYPE_TARGET);
		$this->commandArguments->addParameter(0, 'msg');
	}

	protected function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(count($args) < 2){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_tell_usage());
			return;
		}

		$player = Practice::getPlayerByPrefix(array_shift($args));
		if($player === null){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::player_not_found());
			return;
		}

		if($player === $sender){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_tell_yourself());
			return;
		}

		PrivateMessages::send($sender, $player, implode(' ', $args));
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_tell_description();
	}
}

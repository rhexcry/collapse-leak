<?php

declare(strict_types=1);

namespace collapse\game\duel\command;

use collapse\command\attribute\OnlyForPlayerCommand;
use collapse\command\CollapseCommand;
use collapse\command\CommandArguments;
use collapse\game\duel\DuelManager;
use collapse\game\duel\form\DuelRequestForm;
use collapse\game\duel\types\DuelMode;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use function array_map;
use function array_values;

#[OnlyForPlayerCommand]
final class DuelCommand extends CollapseCommand{

	public function __construct(
		private readonly DuelManager $duelManager
	){
		parent::__construct('duel', 'Sent duel request');
		$this->setPermission('collapse.command.duel');
		$this->commandArguments = new CommandArguments();
		$this->commandArguments->addParameter(0, 'player', AvailableCommandsPacket::ARG_TYPE_TARGET, true);
		$this->commandArguments->addEnum(0, 'mode', array_values(array_map(static function(DuelMode $mode) : string{
			return $mode->value;
		}, DuelMode::cases())), true);
	}

	/**
	 * @param CollapsePlayer $sender
	 */
	public function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(!isset($args[0])){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_duel_usage());
			return;
		}

		if(!$this->duelManager->getPlugin()->getLobbyManager()->isInLobby($sender)){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::only_in_lobby());
			return;
		}

		$player = Practice::getPlayerByPrefix($args[0]);
		if($player === null){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::player_not_found());
			return;
		}

		if($player === $sender){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_duel_yourself());
			return;
		}

		if(!$this->duelManager->getPlugin()->getLobbyManager()->isInLobby($player)){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::player_not_in_lobby());
			return;
		}

		if(isset($args[1]) && ($mode = DuelMode::tryFrom($args[1])) !== null){
			$this->duelManager->getRequestManager()->send($player, $sender, $mode);
		}else{
			$sender->sendForm(new DuelRequestForm($sender, $player));
		}
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_duel_description();
	}
}

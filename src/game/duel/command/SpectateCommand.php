<?php

declare(strict_types=1);

namespace collapse\game\duel\command;

use collapse\command\attribute\OnlyForPlayerCommand;
use collapse\command\CollapseCommand;
use collapse\command\CommandArguments;
use collapse\game\duel\Duel;
use collapse\game\duel\DuelManager;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

#[OnlyForPlayerCommand]
final class SpectateCommand extends CollapseCommand{

	public function __construct(
		private readonly DuelManager $duelManager
	){
		parent::__construct('spectate', 'Spectate a match', 'Usage: /spectate <player>', ['s', 'spec']);
		$this->setPermission('collapse.command.spectate');
		$this->commandArguments = new CommandArguments();
		$this->commandArguments->addParameter(0, 'player', AvailableCommandsPacket::ARG_TYPE_TARGET);
	}

	/**
	 * @param CollapsePlayer $sender
	 */
	public function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(!isset($args[0])){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_spectate_usage());
			return;
		}

		$lobbyManager = $this->duelManager->getPlugin()->getLobbyManager();
		if(!$lobbyManager->isInLobby($sender)){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_spectate_not_in_lobby());
			return;
		}

		$target = Practice::getPlayerByPrefix($args[0]);
		if($target === null){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::player_not_found());
			return;
		}

		$duel = $target->getGame();
		if(!$duel instanceof Duel){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_spectate_target_not_in_game($target->getNameWithRankColor()));
			return;
		}

		if($target->getWorld() !== $duel->getWorldManager()->getWorld()){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_spectate_target_not_in_game($target->getNameWithRankColor()));
			return;
		}

		$lobbyManager->removeFromLobby($sender);
		$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_spectate_successfully($target->getNameWithRankColor()));
		$duel->getSpectatorManager()->addSpectator($sender, $target);
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_spectate_description();
	}
}

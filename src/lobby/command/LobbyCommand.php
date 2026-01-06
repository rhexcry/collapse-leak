<?php

declare(strict_types=1);

namespace collapse\lobby\command;

use collapse\command\attribute\OnlyForPlayerCommand;
use collapse\command\CollapseCommand;
use collapse\i18n\CollapseTranslationFactory;
use collapse\lobby\LobbyManager;
use collapse\player\CollapsePlayer;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;

#[OnlyForPlayerCommand]
final class LobbyCommand extends CollapseCommand{

	public function __construct(
		private readonly LobbyManager $lobbyManager
	){
		parent::__construct('lobby', 'Teleport to lobby', '/lobby', ['hub', 'spawn']);
		$this->setPermission('collapse.command.lobby');
	}

	/**
	 * @param CollapsePlayer $sender
	 */
	public function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		$this->lobbyManager->sendToLobby($sender);
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_lobby_description();
	}
}

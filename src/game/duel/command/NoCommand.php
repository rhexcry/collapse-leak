<?php

declare(strict_types=1);

namespace collapse\game\duel\command;

use collapse\command\attribute\OnlyForPlayerCommand;
use collapse\command\CollapseCommand;
use collapse\game\duel\DuelManager;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\resourcepack\Font;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;

#[OnlyForPlayerCommand]
final class NoCommand extends CollapseCommand{

	public function __construct(
		private readonly DuelManager $duelManager
	){
		parent::__construct('no', 'Decline duel');
		$this->setPermission('collapse.command.no');
	}

	/**
	 * @param CollapsePlayer $sender
	 */
	public function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(!$this->duelManager->getPlugin()->getLobbyManager()->isInLobby($sender)){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::only_in_lobby());
			return;
		}

		$requestManager = Practice::getInstance()->getDuelManager()->getRequestManager();
		if(empty($requestManager->getRequests($sender))){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::duels_form_no_incoming_invites());
			return;
		}

		$lastRequest = $requestManager->removeLastRequest($sender);
		if($lastRequest !== null){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::duel_from_declined(Font::minecraftColorToUnicodeFont(Practice::getPlayerByXuid($lastRequest->getSenderXuid())->getNameWithRankColor())));
		}
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_no_description();
	}
}

<?php

declare(strict_types=1);

namespace collapse\system\party\command;

use collapse\command\attribute\OnlyForPlayerCommand;
use collapse\command\CollapseCommand;
use collapse\command\CommandArguments;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use function array_map;
use function array_reverse;
use function implode;

#[OnlyForPlayerCommand]
final class PartyCommand extends CollapseCommand{

	public function __construct(){
		parent::__construct('party');
		$this->setPermission('collapse.command.party');
		$this->commandArguments = new CommandArguments();
		$this->commandArguments->addEnum(0, 'argument', ['create', 'disband', 'info', 'leave', 'invite', 'accept'], true);
	}

	/**
	 * @param CollapsePlayer $sender
	 */
	protected function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		$partyManager = Practice::getInstance()->getPartyManager();

		if(!isset($args[0])){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_party_usage());
			return;
		}

		switch($args[0]){
			case 'create':
				if($partyManager->isInParty($sender)){
					$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_party_already_in_party());
					return;
				}

				$partyManager->createParty($sender);
				$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_party_created());
				break;
			case 'disband':
				if(!$partyManager->isInParty($sender)){
					$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_party_not_in_party());
					return;
				}

				$party = $partyManager->getPlayerParty($sender);
				if($party->getLeader() !== $sender){
					$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_party_not_leader());
					return;
				}

				$partyManager->disbandParty($party);
				$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_party_disbanded());
				break;
			case 'leave':
				if(!$partyManager->isInParty($sender)){
					$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_party_not_in_party());
					return;
				}

				$party = $partyManager->getPlayerParty($sender);
				if($party->getLeader() === $sender){
					$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_party_leave_cant_leader());
					return;
				}

				$party->removeMember($sender);
				$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_party_left());
				foreach($party->getMembers() as $member){
					$member = Practice::getPlayerByXuid($member);
					if($member === null){
						continue;
					}

					$member->sendTranslatedMessage(CollapseTranslationFactory::command_party_left_members($sender->getNameWithRankColor()));
				}
				break;
			case 'info':
				if(!$partyManager->isInParty($sender)){
					$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_party_not_in_party());
					return;
				}

				$party = $partyManager->getPlayerParty($sender);
				$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_party_info_message(
					$party->getLeader()->getNameWithRankColor(),
					implode('&f, &b', array_map(function(CollapsePlayer $player) : string{
						return $player->getNameWithRankColor();
					}, $party->getMembers()))));
				break;
			case 'invite':
				if(!$partyManager->isInParty($sender)){
					$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_party_not_in_party());
					return;
				}

				$party = $partyManager->getPlayerParty($sender);
				if($party->getLeader() !== $sender){
					$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_party_not_leader());
					return;
				}

				if(!isset($args[1])){
					$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_party_args_invite_usage());
					return;
				}

				$target = Practice::getPlayerByPrefix($args[1]);
				if($target === null){
					$this->sendTranslatedMessage($sender, CollapseTranslationFactory::player_not_found());
					return;
				}

				if($partyManager->isInParty($target)){
					$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_party_player_already_in_party($target->getNameWithRankColor()));
					return;
				}

				$party = $partyManager->getPlayerParty($sender);
				$party->invitePlayer($target);
				break;
			case 'accept':
				if($partyManager->isInParty($sender)){
					$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_party_already_in_party());
					return;
				}

				if(isset($args[1])){
					$from = Practice::getPlayerByPrefix($args[1]);
					if($from === null){
						$this->sendTranslatedMessage($sender, CollapseTranslationFactory::player_not_found());
						return;
					}

					$party = $partyManager->getPlayerParty($from);
					if($party === null){
						$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_party_player_dont_have_party($from->getNameWithRankColor()));
						return;
					}

					if(!$party->isInvited($sender)){
						$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_party_dont_have_invite_in_party($from->getNameWithRankColor()));
						return;
					}

					$party->addMember($sender);
					return;
				}

				$parties = array_reverse($partyManager->getAllParties());
				foreach($parties as $party){
					if($party->isInvited($sender)){
						$party->addMember($sender);
						return;
					}
				}
				break;
		}
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_party_description();
	}
}
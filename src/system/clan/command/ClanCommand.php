<?php

declare(strict_types=1);

namespace collapse\system\clan\command;

use collapse\command\attribute\OnlyForPlayerCommand;
use collapse\command\CollapseCommand;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\system\clan\ClanManager;
use collapse\system\clan\form\ClanInfoForm;
use collapse\system\clan\form\EditClanForm;
use collapse\system\clan\types\ClanError;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use function count;
use function implode;
use function in_array;

#[OnlyForPlayerCommand]
final class ClanCommand extends CollapseCommand{

	private const array POSSIBLE_ARGS = ['create', 'disband', 'leave', 'invite', 'delete', 'info', 'edit'];

	public function __construct(private readonly ClanManager $clanManager){
		parent::__construct('clan', aliases: ['c']);
		$this->setPermission('collapse.command.clan');
		// TODO: command args
	}

	/**
	 * @param CollapsePlayer $sender
	 */
	protected function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(count($args) < 1 || !in_array($args[0], self::POSSIBLE_ARGS, true)){
			$this->sendTranslatedMessage($sender, $this->getUsage());
			return;
		}

		switch($args[0]){
			case 'create':
				if($sender->getProfile()->getClanId() !== null){
					$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_clan_already_has_clan());
					return;
				}

				if(count($args) < 3){
					$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_clan_subarg_create_usage());
					return;
				}

				$clanName = $args[1];
				$tag = $args[2];

				$error = $this->clanManager->createClan($sender, $clanName, $tag);

				if($error !== null){
					match($error){
						ClanError::AlreadyHasClanWithName => $this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_clan_clan_with_name_exists($clanName)),
						ClanError::AlreadyHasClanWithTag => $this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_clan_clan_with_tag_exists($tag)),
					};
					return;
				}

				$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_clan_successfully_created($clanName, $tag));
				break;
			case 'delete':
				$clan = $sender->getProfile()->getClan();
				if($clan === null){
					$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_clan_dont_have_clan());
					return;
				}

				if($clan->getLeaderXuid() !== $sender->getProfile()->getXuid()){
					$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_clan_role_too_low());
					return;
				}

				$this->clanManager->disbandClan($clan);
				$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_clan_successfully_deleted($clan->getName()));
				break;
			case 'info':
				$clan = $sender->getProfile()->getClan();
				if($clan === null){
					$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_clan_dont_have_clan());
					return;
				}

				$sender->sendForm(new ClanInfoForm($sender));
				break;
				/*$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_clan_subarg_info_title($clan->getName()));
				$sender->sendTranslatedMessage(CollapseTranslationFactory::command_clan_subarg_info_message(
					$clan->getName(),
					$clan->getTag(),
					Practice::getInstance()->getProfileManager()->getProfileByXuid($clan->getLeaderXuid())->getPlayerName(),
					(string) $clan->getTreasury(),
					(string) $clan->getMemberCount(),
					(string) $clan->getSlots(),
					implode(', ', array_map(function(ClanMember $member){
						return Practice::getInstance()->getProfileManager()->getProfileByXuid($member->getXuid())->getPlayerName();
					}, $clan->getMembers())),
					(string) $clan->getWins(),
					(string) $clan->getLosses()
				), false);
				break;*/
			case 'edit':
				$clan = $sender->getProfile()->getClan();
				if($clan === null){
					$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_clan_dont_have_clan());
					return;
				}

				$sender->sendForm(new EditClanForm($sender));
				break;

		}

	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_clan_description();
	}

	public function getUsage() : Translatable|string{
		return CollapseTranslationFactory::command_clan_usage(implode(', ', self::POSSIBLE_ARGS));
	}

	private function checkFor(){

	}
}

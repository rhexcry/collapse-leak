<?php

declare(strict_types=1);

namespace collapse\punishments\command;

use collapse\command\CollapseCommand;
use collapse\command\CommandArguments;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\player\rank\attribute\RequiresRank;
use collapse\player\rank\Rank;
use collapse\Practice;
use collapse\punishments\PunishmentManager;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use function count;

#[RequiresRank(Rank::MODERATOR)]
final class UnmuteCommand extends CollapseCommand{

	public function __construct(private readonly PunishmentManager $punishmentManager){
		parent::__construct('unmute');
		$this->setPermission('collapse.command.unmute');
		$this->commandArguments = new CommandArguments();
		$this->commandArguments->addParameter(0, 'player');
	}

	protected function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(count($args) < 1){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_unmute_usage());
			return;
		}

		$punishment = $this->punishmentManager->getMutePunishmentByName(Practice::getPlayerByPrefix($args[0])?->getName() ?? $args[0]);
		if($punishment === null){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_unmute_not_muted());
			return;
		}

		$this->punishmentManager->unmute($punishment, $sender);
		$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_unmute_successfully(
			$punishment->getPlayerName()
		));
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_unmute_description();
	}
}

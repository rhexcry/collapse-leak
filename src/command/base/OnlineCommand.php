<?php

declare(strict_types=1);

namespace collapse\command\base;

use collapse\command\CollapseCommand;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\player\rank\attribute\RequiresRank;
use collapse\player\rank\Rank;
use collapse\Practice;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use function implode;

#[RequiresRank(Rank::MODERATOR)]
final class OnlineCommand extends CollapseCommand{

	public function __construct(){
		parent::__construct('collapse');
		$this->setPermission('collapse.command.online');
	}

	protected function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		$onlineList = implode('&f, &e', Practice::onlinePlayers());
		$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_online_message((string) count(Practice::onlinePlayers()), $onlineList));
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_online_description();
	}
}
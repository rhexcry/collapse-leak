<?php

declare(strict_types=1);

namespace collapse\report\command;

use collapse\command\attribute\OnlyForPlayerCommand;
use collapse\command\CollapseCommand;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\player\rank\attribute\RequiresRank;
use collapse\player\rank\Rank;
use collapse\report\form\ReportsForm;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;

#[OnlyForPlayerCommand]
#[RequiresRank(Rank::MODERATOR)]
final class ReportsCommand extends CollapseCommand{

	public function __construct(){
		parent::__construct('reports');
		$this->setPermission('collapse.command.reports');
	}

	/**
	 * @param CollapsePlayer $sender
	 */
	protected function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		$sender->sendForm(new ReportsForm());
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_reports_description();
	}
}

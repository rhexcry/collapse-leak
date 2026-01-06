<?php

declare(strict_types=1);

namespace collapse\system\restart\command;

use collapse\command\CollapseCommand;
use collapse\command\CommandArguments;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\player\rank\attribute\RequiresRank;
use collapse\player\rank\Rank;
use collapse\system\restart\RestartManager;
use collapse\utils\TimeUtils;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use function count;

#[RequiresRank(Rank::OWNER)]
final class RestartCommand extends CollapseCommand{

	public function __construct(private readonly RestartManager $restartManager){
		parent::__construct('restart', 'Restart management');
		$this->setPermission('collapse.command.restart');
		$this->commandArguments = new CommandArguments();
		$this->commandArguments->addEnum(0, 'option', ['force', 'remaining'], true);
	}

	protected function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(count($args) < 1){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_restart_usage());
			return;
		}

		switch($args[0]){
			case 'force':
				$this->restartManager->forceRestart(5);
				break;
			case 'remaining':
				$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_restart_remaining(TimeUtils::convert($this->restartManager->getTimeLeft())));
				break;
		}
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_restart_description();
	}
}
<?php

declare(strict_types=1);

namespace collapse\report\command;

use collapse\command\attribute\OnlyForPlayerCommand;
use collapse\command\CollapseCommand;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use function count;

#[OnlyForPlayerCommand]
final class FeedBackCommand extends CollapseCommand{

	protected function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(count($args) < 1){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_feedback_usage());
			return;
		}

	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_feedback_description();
	}
}

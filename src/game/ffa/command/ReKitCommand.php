<?php

declare(strict_types=1);

namespace collapse\game\ffa\command;

use collapse\command\attribute\OnlyForPlayerCommand;
use collapse\command\CollapseCommand;
use collapse\game\ffa\FreeForAllArena;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;

#[OnlyForPlayerCommand]
final class ReKitCommand extends CollapseCommand{

	public function __construct(){
		parent::__construct('rekit', 'Re-kit');
		$this->setPermission('collapse.command.rekit');
	}

	/**
	 * @param CollapsePlayer $sender
	 */
	public function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		$arena = $sender->getGame();
		if(!$arena instanceof FreeForAllArena){
			$sender->sendTranslatedMessage(CollapseTranslationFactory::command_re_kit_not_in_arena());
			return;
		}

		if(!$arena->isAntiInterrupt()){
			$sender->sendTranslatedMessage(CollapseTranslationFactory::command_re_kit_unavailable());
			return;
		}

		if($arena->getOpponentManager()->getOpponent($sender) !== null){
			$sender->sendTranslatedMessage(CollapseTranslationFactory::command_re_kit_in_combat());
			return;
		}
		if($arena->getRespawnManager()->hasRespawnTask($sender)){
			$sender->sendTranslatedMessage(CollapseTranslationFactory::command_re_kit_respawning());
			return;
		}

		$arena->getPlayerManager()->reset($sender);
		$sender->sendTranslatedMessage(CollapseTranslationFactory::command_re_kit_successfully());
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_re_kit_description();
	}
}

<?php

declare(strict_types=1);

namespace collapse\command\base;

use collapse\command\attribute\OnlyForPlayerCommand;
use collapse\command\CollapseCommand;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\player\rank\attribute\RequiresRank;
use collapse\player\rank\Rank;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use function count;

#[OnlyForPlayerCommand]
#[RequiresRank(Rank::OWNER)]
final class WorldCommand extends CollapseCommand{

	public function __construct(){
		parent::__construct('world', 'World management');
		$this->setPermission('collapse.command.world');
	}

	/**
	 * @param CollapsePlayer $sender
	 */
	public function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(!isset($args[0])){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_world_usage());
			return;
		}

		switch($args[0]){
			case 'tp':
				if(count($args) < 2){
					$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_world_usage());
					return;
				}

				$worldManager = $sender->getServer()->getWorldManager();
				if(!$worldManager->loadWorld($args[1])){
					$sender->sendMessage(CollapseTranslationFactory::command_world_unknown_world());
					return;
				}
				$sender->teleport($worldManager->getWorldByName($args[1])->getSpawnLocation());
				break;
			case 'save':
				if(count($args) < 2){
					$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_world_usage());
					return;
				}

				$worldManager = $sender->getServer()->getWorldManager();
				if(!$worldManager->loadWorld($args[1])){
					$sender->sendMessage(CollapseTranslationFactory::command_world_unknown_world());
					return;
				}
				$worldManager->getWorldByName($args[1])?->save(true);
				$sender->sendTranslatedMessage(CollapseTranslationFactory::command_world_save_success($args[1]));
				break;
		}
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_world_description();
	}
}

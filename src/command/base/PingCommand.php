<?php

declare(strict_types=1);

namespace collapse\command\base;

use collapse\command\CollapseCommand;
use collapse\command\CommandArguments;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use function count;

final class PingCommand extends CollapseCommand{

	public function __construct(){
		parent::__construct('ping', 'Get ping', '/ping <player>');
		$this->setPermission('collapse.command.ping');
		$this->commandArguments = new CommandArguments();
		$this->commandArguments->addParameter(0, 'player', AvailableCommandsPacket::ARG_TYPE_TARGET, isOptional: true);
	}

	/**
	 * @param CollapsePlayer $sender
	 */
	protected function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(count($args) < 1){
			if(!$sender instanceof CollapsePlayer){
				$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_ping_usage());
				return;
			}

			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_ping_yourself((string) $sender->getNetworkSession()->getPing()));
			return;
		}

		$player = Practice::getPlayerByPrefix($args[0]);

		if($player === null){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::player_not_found());
			return;
		}

		$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_ping_other_player(
			$player->getNameWithRankColor(),
			(string) $player->getNetworkSession()->getPing()
		));
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_ping_description();
	}
}

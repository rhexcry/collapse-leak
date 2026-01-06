<?php

declare(strict_types=1);

namespace collapse\command\base;

use collapse\command\attribute\OnlyForPlayerCommand;
use collapse\command\CollapseCommand;
use collapse\command\CommandArguments;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\player\rank\attribute\RequiresRank;
use collapse\player\rank\Rank;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\types\BoolGameRule;

#[OnlyForPlayerCommand]
#[RequiresRank(Rank::OWNER)]
final class ShowCoordinatesCommand extends CollapseCommand{

	public function __construct(){
		parent::__construct('showcoordinates', 'Toggle a show coordinates');
		$this->setPermission('collapse.command.showcoordinates');
		$this->commandArguments = new CommandArguments();
		$this->commandArguments->addEnum(0, 'toggle', ['on', 'off'], true);
	}

	/**
	 * @param CollapsePlayer $sender
	 */
	public function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(!isset($args[0])){
			$sender->sendMessage((string) $sender->getLocation());
			return;
		}
		$sender->getNetworkSession()->sendDataPacket(GameRulesChangedPacket::create([
			'showcoordinates' => new BoolGameRule($args[0] === 'on', false)
		]));
		$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_showcoordinates_changed());
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_showcoordinates_description();
	}
}

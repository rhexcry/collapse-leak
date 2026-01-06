<?php

declare(strict_types=1);

namespace collapse\system\observe\command;

use collapse\command\attribute\OnlyForPlayerCommand;
use collapse\command\CollapseCommand;
use collapse\command\CommandArguments;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\player\rank\attribute\RequiresRank;
use collapse\player\rank\Rank;
use collapse\Practice;
use collapse\system\observe\ObserveManager;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use function count;

#[OnlyForPlayerCommand]
#[RequiresRank(Rank::MODERATOR)]
final class ObserveCommand extends CollapseCommand{

	public function __construct(
		private readonly ObserveManager $observeManager
	){
		parent::__construct('observe');
		$this->setPermission('collapse.command.observe');
		$this->commandArguments = new CommandArguments();
		$this->commandArguments->addParameter(0, 'player', AvailableCommandsPacket::ARG_TYPE_TARGET);
	}

	/**
	 * @param CollapsePlayer $sender
	 */
	protected function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(count($args) < 1){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_observe_usage());
			return;
		}

		$target = Practice::getPlayerByPrefix($args[0]);
		if($target === null){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::player_not_found());
			return;
		}

		if($this->observeManager->isObserving($sender)){
			$this->observeManager->stopObserving($sender);
		}
		$this->observeManager->startObserving($sender, $target);
		$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_observe_start($target->getNameWithRankColor()));
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_observe_description();
	}
}

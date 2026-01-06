<?php

declare(strict_types=1);

namespace collapse\player\rank\command;

use collapse\command\CollapseCommand;
use collapse\command\CommandArguments;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\player\rank\attribute\RequiresRank;
use collapse\player\rank\Rank;
use collapse\player\rank\RankManager;
use collapse\Practice;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use function count;
use function implode;

#[RequiresRank(Rank::ADMIN)]
class SetRankCommand extends CollapseCommand{

	public function __construct(private readonly RankManager $rankManager){
		parent::__construct('setrank', 'Change a player\'s rank');
		$this->setPermission('collapse.command.setrank');
		$this->commandArguments = new CommandArguments();
		$this->commandArguments->addParameter(0, 'player', AvailableCommandsPacket::ARG_TYPE_TARGET);
		$this->commandArguments->addEnum(0, 'rank', Rank::values());
	}

	protected function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(count($args) < 2){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_set_rank_usage());
			return;
		}

		if(($player = Practice::getPlayerByPrefix($args[0])) !== null){
			$profile = $player->getProfile();
		}else{
			$profile = Practice::getInstance()->getProfileManager()->getProfileByName($args[0]);
			if($profile === null){
				$this->sendTranslatedMessage($sender, CollapseTranslationFactory::player_not_registered());
				return;
			}
		}

		$rank = Rank::tryFrom($args[1]);

		if($rank === null){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_set_rank_rank_not_found(implode(', ', Rank::values())));
			return;
		}

		if($sender instanceof CollapsePlayer){
			if($sender->getProfile()->getRank()->getPriority() < $rank->getPriority()){
				$this->sendTranslatedMessage($sender, CollapseTranslationFactory::no_permission());
				return;
			}
		}

		$this->rankManager->setRank($profile, $rank);
		$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_set_rank_successfully(
			$profile->getPlayerName(),
			$rank->toDisplayName()
		));
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_set_rank_description();
	}
}
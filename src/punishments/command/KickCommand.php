<?php

declare(strict_types=1);

namespace collapse\punishments\command;

use collapse\command\CollapseCommand;
use collapse\command\CommandArguments;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\player\rank\attribute\RequiresRank;
use collapse\player\rank\Rank;
use collapse\Practice;
use collapse\punishments\PunishmentManager;
use collapse\punishments\rule\PunishmentRules;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use function array_shift;
use function count;
use function implode;

#[RequiresRank(Rank::MODERATOR)]
final class KickCommand extends CollapseCommand{

	public function __construct(private readonly PunishmentManager $punishmentManager){
		parent::__construct('kick', 'Kick a player');
		$this->setPermission('collapse.command.kick');
		$this->commandArguments = new CommandArguments();
		$this->commandArguments->addParameter(0, 'player', AvailableCommandsPacket::ARG_TYPE_TARGET);
		$this->commandArguments->addEnum(0, 'reason', array_keys(PunishmentRules::getAllRules()));
	}

	protected function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(count($args) < 2){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_kick_usage());
			return;
		}

		$player = Practice::getPlayerByPrefix(array_shift($args));
		if($player === null){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::player_not_found());
			return;
		}

		$reason = implode(' ', $args);
		$rule = PunishmentRules::getRule($reason);

		$kickReason = $rule?->getCroppedDescription() ?? $reason;
		$this->punishmentManager->kick($player, $kickReason, $sender);

		$displayReason = $rule?->getTranslation(true) ?? $reason;
		$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_kick_successfully($player->getName(), $displayReason));
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_kick_description();
	}
}
<?php

declare(strict_types=1);

namespace collapse\punishments\command;

use collapse\command\CollapseCommand;
use collapse\command\CommandArguments;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\player\profile\trait\PlayerProfileResolver;
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
use function time;

#[RequiresRank(Rank::MODERATOR)]
final class MuteCommand extends CollapseCommand{
	use PlayerProfileResolver;

	private array $confirmations = [];

	public function __construct(private readonly PunishmentManager $punishmentManager){
		parent::__construct('mute');
		$this->setPermission('collapse.command.mute');
		$this->commandArguments = new CommandArguments();
		$this->commandArguments->addParameter(0, 'player', AvailableCommandsPacket::ARG_TYPE_TARGET);
		$this->commandArguments->addEnum(0, 'reason', array_keys(PunishmentRules::getAllRules()));
	}

	/**
	 * @param CollapsePlayer $sender
	 */
	protected function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(count($args) < 2){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_mute_usage());
			return;
		}

		$playerName = array_shift($args);
		$player = Practice::getPlayerByPrefix($playerName) ?? $playerName;

		$profile = self::resolveProfile($player);
		if($profile === null){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::player_not_registered());
			return;
		}

		if($player === $sender){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_mute_yourself());
			return;
		}

		if($this->punishmentManager->isMutedByXuid($profile->getXuid())){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_mute_already_muted());
			return;
		}

		$reason = implode(' ', $args);
		$senderName = $sender->getName();
		$rule = PunishmentRules::getRule($reason);

		if($rule === null){
			if(!isset($this->confirmations[$senderName]) ||
				$this->confirmations[$senderName]['player'] !== $profile->getXuid() ||
				$this->confirmations[$senderName]['reason'] !== $reason
			){
				$this->confirmations[$senderName] = [
					'player' => $profile->getXuid(),
					'reason' => $reason
				];
				$this->sendTranslatedMessage(
					$sender,
					CollapseTranslationFactory::command_mute_enter_again_for_mute($profile->getPlayerName(), $reason)
				);
				return;
			}

			$this->punishmentManager->mute($profile, $reason, $sender, null, function() use ($sender, $profile, $reason, $rule) : void{
				if($sender === null){
					return;
				}
				$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_mute_successfully(
					$profile->getPlayerName(),
					$reason
				));
			});
			unset($this->confirmations[$sender->getName()]);
			return;
		}

		$expires = $rule->getDuration() === null ? null : time() + $rule->getDuration();

		$this->punishmentManager->mute($profile, $rule->getCroppedDescription(), $sender, $expires, function() use ($sender, $profile, $rule) : void{
			if($sender === null){
				return;
			}
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_mute_successfully(
				$profile->getPlayerName(),
				$rule?->getTranslation(true) ?? $rule->getCroppedDescription()
			));
		});
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_mute_description();
	}
}

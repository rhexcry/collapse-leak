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
final class BanCommand extends CollapseCommand{
	use PlayerProfileResolver;

	private array $confirmations = [];

	public function __construct(private readonly PunishmentManager $punishmentManager){
		parent::__construct('ban');
		$this->setPermission('collapse.command.ban');
		$this->commandArguments = new CommandArguments();
		$this->commandArguments->addParameter(0, 'player', AvailableCommandsPacket::ARG_TYPE_TARGET);
		$this->commandArguments->addEnum(0, 'reason', array_keys(PunishmentRules::getAllRules()));
	}

	protected function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(count($args) < 2){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_ban_usage());
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
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_ban_yourself());
			return;
		}

		if($this->punishmentManager->isBannedByXuid($profile->getXuid())){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_ban_already_banned());
			return;
		}

		$reason = implode(' ', $args);
		$rule = PunishmentRules::getRule($reason);
		$senderName = $sender->getName();

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
					CollapseTranslationFactory::command_ban_enter_again_for_ban($profile->getPlayerName(), $reason)
				);
				return;
			}

			$this->punishmentManager->ban(
				$profile,
				$reason,
				$sender,
				null,
				function() use ($sender, $profile, $reason){
					if($sender !== null){
						$this->sendTranslatedMessage(
							$sender,
							CollapseTranslationFactory::command_ban_successfully(
								$profile->getPlayerName(),
								$reason
							)
						);
						unset($this->confirmations[$sender->getName()]);
					}
				}
			);

			return;
		}

		$this->punishmentManager->ban(
			$profile,
			$rule->getCroppedDescription(),
			$sender,
			$rule->getDuration() ? time() + $rule->getDuration() : null,
			function() use ($sender, $profile, $rule){
				if($sender !== null){
					$this->sendTranslatedMessage(
						$sender,
						CollapseTranslationFactory::command_ban_successfully(
							$profile->getPlayerName(),
							$rule->getTranslation(true) ?? $rule->getCroppedDescription()
						)
					);
				}
			}
		);
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_ban_description();
	}
}

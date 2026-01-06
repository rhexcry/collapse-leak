<?php

declare(strict_types=1);

namespace collapse\player\profile\command;

use collapse\command\CollapseCommand;
use collapse\command\CommandArguments;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\client\DeviceUtils;
use collapse\player\client\InputModeUtils;
use collapse\player\CollapsePlayer;
use collapse\player\profile\ProfileManager;
use collapse\player\profile\trait\PlayerProfileResolver;
use collapse\player\rank\attribute\RequiresRank;
use collapse\player\rank\Rank;
use collapse\Practice;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\lang\Translatable;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\utils\TextFormat;

#[RequiresRank(Rank::MODERATOR)]
final class ProfileCommand extends CollapseCommand{
	use PlayerProfileResolver;

	public function __construct(
		private readonly ProfileManager $profileManager
	){
		parent::__construct('profile', 'Check player\'s profile');
		$this->setPermission('collapse.command.profile');
		$this->commandArguments = new CommandArguments();
		$this->commandArguments->addParameter(0, 'player', AvailableCommandsPacket::ARG_TYPE_TARGET);
	}

	public function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(!isset($args[0])){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_profile_usage());
			return;
		}

		$profile = self::resolveProfile(Practice::getPlayerByPrefix($args[0]) ?? $args[0]);
		if($profile === null){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::player_not_registered());
			return;
		}

		$player = Practice::getPlayerByXuid($profile->getXuid());
		if($sender instanceof ConsoleCommandSender || ($sender instanceof CollapsePlayer && $sender->getProfile()->getRank() === Rank::OWNER)){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_profile_staff_advanced(
				$profile->getPlayerName(),
				$player !== null ? TextFormat::GREEN . 'Online' : TextFormat::RED . 'Offline',
				$sender instanceof CollapsePlayer ? $profile->getRank()->toFont() : $profile->getRank()->toDisplayName(),
				$profile->getGameVersion(),
				(new \DateTime())->setTimestamp($profile->getFirstJoinTime())->format('Y-m-d G:i:s'),
				InputModeUtils::toDisplayName($profile->getInputMode()),
				DeviceUtils::toDisplayName($profile->getDeviceOS()),
				$profile->getDeviceModel()
			), false);
		}else{
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_profile_basic(
				$profile->getPlayerName(),
				$player !== null ? TextFormat::GREEN . 'Online' : TextFormat::RED . 'Offline',
				$sender instanceof CollapsePlayer ? $profile->getRank()->toFont() : $profile->getRank()->toDisplayName(),
				$profile->getGameVersion(),
				(new \DateTime())->setTimestamp($profile->getFirstJoinTime())->format('Y-m-d G:i:s'),
				InputModeUtils::toDisplayName($profile->getInputMode()),
				DeviceUtils::toDisplayName($profile->getDeviceOS())
			), false);
		}
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_profile_description();
	}
}

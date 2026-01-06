<?php

declare(strict_types=1);

namespace collapse\player\profile\command;

use collapse\command\CollapseCommand;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\player\profile\Profile;
use collapse\player\profile\ProfileManager;
use collapse\player\profile\trait\PlayerProfileResolver;
use collapse\player\rank\attribute\RequiresRank;
use collapse\player\rank\Rank;
use collapse\Practice;
use MongoDB\Model\BSONDocument;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\utils\TextFormat;
use function array_map;
use function count;
use function implode;
use const PHP_EOL;

#[RequiresRank(Rank::MODERATOR)]
final class AltsCommand extends CollapseCommand{
	use PlayerProfileResolver;

	public function __construct(
		private readonly ProfileManager $profileManager
	){
		parent::__construct('alts', 'Check player\'s alts accounts');
		$this->setPermission('collapse.command.alts');
	}

	public function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(!isset($args[0])){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_alts_usage());
			return;
		}

		$profile = self::resolveProfile(Practice::getPlayerByPrefix($args[0]) ?? $args[0]);
		if($profile === null){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::player_not_registered());
			return;
		}

		if($sender instanceof CollapsePlayer){
			if($sender->getProfile()->getRank() !== Rank::OWNER){
				$this->profileManager->getPlugin()->getSocialManager()->getStaffLogger()->onCheckAltsAccounts($sender, $profile);
			}
		}else{
			$this->profileManager->getPlugin()->getSocialManager()->getStaffLogger()->onCheckAltsAccounts($sender, $profile);
		}

		$this->profileManager->findAltsAccounts($profile)->onResolve(function(array $result) use ($sender, $profile) : void{
			if($sender === null){
				return;
			}

			$plugin = Practice::getInstance();

			$formatName = function(BSONDocument $document) use ($plugin) : string{
				$profile = Profile::fromBsonDocument($document);
				$playerName = $profile->getPlayerName();

				$isBanned = $plugin->getPunishmentManager()->isBannedByName($playerName);
				$isMuted = $plugin->getPunishmentManager()->isMutedByName($playerName);

				if($isBanned){
					return TextFormat::RED . $playerName . TextFormat::RESET;
				}
				if($isMuted){
					return TextFormat::YELLOW . $playerName . TextFormat::RESET;
				}

				return TextFormat::AQUA . $playerName;
			};

			$ipMatches = implode(TextFormat::GRAY . ', ', array_map($formatName, $result['ip']));
			$deviceMatches = implode(TextFormat::GRAY . ', ', array_map($formatName, $result['device_id']));

			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_alts_matches(
				$profile->getPlayerName(),
				$ipMatches,
				$deviceMatches
			));

			$bannedAlts = [];
			$mutedAlts = [];

			$allAlts = array_merge($result['ip'], $result['device_id']);
			foreach($allAlts as $document){
				$altProfile = Profile::fromBsonDocument($document);
				$playerName = $altProfile->getPlayerName();

				$banPunishment = $plugin->getPunishmentManager()->getBanPunishmentByXuid($altProfile->getXuid());
				if($banPunishment !== null && !isset($bannedAlts[$playerName])){
					$bannedAlts[$playerName] = $banPunishment;
				}

				$mutePunishment = $plugin->getPunishmentManager()->getMutePunishmentByXuid($altProfile->getXuid());
				if($mutePunishment !== null && !isset($mutedAlts[$playerName])){
					$mutedAlts[$playerName] = $mutePunishment;
				}
			}

			if(count($bannedAlts) > 0 || count($mutedAlts) > 0){
				$sender->sendMessage(PHP_EOL, false);
			}

			foreach($bannedAlts as $playerName => $banPunishment){
				$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_alts_ban_info(
					$playerName,
					$banPunishment->getReason(),
					$banPunishment->getSender(),
					$banPunishment->getExpiration() === null ?
						CollapseTranslationFactory::punishment_expires_never() :
						(new \DateTime())->setTimestamp($banPunishment->getExpiration())->format('Y-m-d H:i:s')
				), false);
			}

			foreach($mutedAlts as $playerName => $mutePunishment){
				$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_alts_mute_info(
					$playerName,
					$mutePunishment->getReason(),
					$mutePunishment->getSender(),
					$mutePunishment->getExpiration() === null ?
						CollapseTranslationFactory::punishment_expires_never() :
						(new \DateTime())->setTimestamp($mutePunishment->getExpiration())->format('Y-m-d H:i:s')
				), false);
			}
		});
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_alts_description();
	}
}

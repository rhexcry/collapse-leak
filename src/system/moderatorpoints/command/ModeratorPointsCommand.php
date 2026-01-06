<?php

declare(strict_types=1);

namespace collapse\system\moderatorpoints\command;

use collapse\command\CollapseCommand;
use collapse\command\CommandArguments;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\player\profile\Profile;
use collapse\player\profile\trait\PlayerProfileResolver;
use collapse\player\rank\attribute\RequiresRank;
use collapse\player\rank\Rank;
use collapse\Practice;
use collapse\system\moderatorpoints\ModeratorPointsManager;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use function array_filter;
use function array_slice;
use function count;
use function intval;
use function is_numeric;
use function strtolower;

#[RequiresRank(Rank::MODERATOR)]
final class ModeratorPointsCommand extends CollapseCommand{
	use PlayerProfileResolver;

	public function __construct(private readonly ModeratorPointsManager $manager){
		parent::__construct('mp', 'Moderator points management');
		$this->setPermission('collapse.command.mp');
		$this->commandArguments = new CommandArguments();
		$this->commandArguments->addEnum(0, 'execute', ['stats', 'subtract', 'add', 'resetall']);
		$this->commandArguments->addParameter(0, 'player', AvailableCommandsPacket::ARG_TYPE_TARGET, true);
		$this->commandArguments->addParameter(0, 'amount', AvailableCommandsPacket::ARG_TYPE_INT, true);
	}

	protected function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(count($args) < 1){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_mp_usage());
			return;
		}

		$action = strtolower($args[0]);
		$restArgs = array_slice($args, 1);

		if($action === "resetall"){
			if(!$sender instanceof CollapsePlayer || $sender->getProfile()->getRank()->getPriority() < Rank::ADMIN->getPriority()){
				$this->sendTranslatedMessage($sender, CollapseTranslationFactory::no_permission());
				return;
			}

			Practice::getInstance()->getProfileManager()->getAllStaffProfiles()->onResolve(
				fn(array $profiles) => $this->processResetAll($sender, $profiles)
			);
			return;
		}

		if(!in_array($action, ["stats", "subtract", "add"], true)){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_mp_usage());
			return;
		}
		if(count($restArgs) < 1){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_mp_usage());
			return;
		}

		$player = Practice::getPlayerByPrefix($restArgs[0]) ?? $restArgs[0];
		$profile = self::resolveProfile($player);
		if($profile === null){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::player_not_registered());
			return;
		}
		if(!$profile->getRank()->isStaffRank()){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::player_not_staff());
			return;
		}

		if($action === "stats"){
			$punishments = $profile->getIssuedPunishments();
			$onlineMinutes = $profile->getOnlineMinutes();

			$totalBans = count(array_filter($punishments, fn(array $p) => $p['type'] === 'ban'));
			$totalMutes = count(array_filter($punishments, fn(array $p) => $p['type'] === 'mute'));
			$totalKicks = count(array_filter($punishments, fn(array $p) => $p['type'] === 'kick'));
			$totalMinutes = count($onlineMinutes);
			$totalMpFromActions = ($totalBans * 4) + ($totalMutes * 2) + ($totalKicks * 3) + ($totalMinutes * 2);
			$totalMp = $profile->getTotalMp();

			$message = CollapseTranslationFactory::command_mp_stats($profile->getPlayerName(), (string)$totalBans, (string)$totalMutes, (string)$totalKicks, (string)$totalMinutes, (string)$totalMpFromActions, (string)$totalMp);
			$this->sendTranslatedMessage($sender, $message);
			return;
		}

		if(!$sender instanceof CollapsePlayer || $sender->getProfile()->getRank()->getPriority() < Rank::ADMIN->getPriority()){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::no_permission());
			return;
		}
		if(count($restArgs) < 2 || !is_numeric($restArgs[1])){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_mp_usage());
			return;
		}

		$amount = intval($restArgs[1]);
		if($amount <= 0){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_mp_invalid_amount());
			return;
		}

		if($action === "subtract"){
			$profile->subtractMp($amount);
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_mp_subtract($profile->getPlayerName(), (string)$amount));
		}else{
			$profile->addMp($amount);
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_mp_add($profile->getPlayerName(), (string)$amount));
		}

		$profile->save();
	}

	private function processResetAll(CollapsePlayer $sender, array $profiles) : void{
		$count = 0;
		foreach($profiles as $doc){
			$profile = Profile::fromBsonDocument($doc);
			$xuid = $profile->getXuid();
			$onlinePlayer = Practice::getPlayerByXuid($xuid);

			if($onlinePlayer?->getProfile() !== null){
				$onlineProfile = $onlinePlayer->getProfile();
				$onlineProfile->setTotalMp(0);
				$onlineProfile->setIssuedPunishments([]);
				$onlineProfile->setOnlineMinutes([]);
				$onlineProfile->save();
				$count++;
				continue;
			}

			$profile->setTotalMp(0);
			$profile->setIssuedPunishments([]);
			$profile->setOnlineMinutes([]);
			$profile->save();
			$count++;
		}

		$sender->sendTranslatedMessage(CollapseTranslationFactory::command_mp_resetall((string)$count));
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_mp_description();
	}
}
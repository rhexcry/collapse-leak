<?php

declare(strict_types=1);

namespace collapse\system\internal\punish\command;

use collapse\command\CollapseCommand;
use collapse\command\CommandArguments;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\player\rank\attribute\RequiresRank;
use collapse\player\rank\Rank;
use collapse\system\internal\punish\PunishManager;
use collapse\system\internal\punish\PunishType;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use function count;
use function time;

#[RequiresRank(Rank::OWNER)]
final class IpCommand extends CollapseCommand{

	public function __construct(
		private readonly PunishManager $punishManager
	){
		parent::__construct('ip', 'IP management');
		$this->setPermission('collapse.command.ip');
		$this->commandArguments = new CommandArguments();
		$this->commandArguments->addEnum(0, 'ip_action', ['ban', 'unban']);
		$this->commandArguments->addParameter(0, 'ip', AvailableCommandsPacket::ARG_TYPE_STRING);
		$this->commandArguments->addParameter(0, 'duration', AvailableCommandsPacket::ARG_TYPE_STRING, true);
		$this->commandArguments->addParameter(0, 'reason', AvailableCommandsPacket::ARG_TYPE_MESSAGE, true);
	}

	protected function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(count($args) < 2){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_ip_usage());
			return;
		}

		$subcommand = array_shift($args);
		$ip = array_shift($args);

		if(!filter_var($ip, FILTER_VALIDATE_IP)){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_ip_invalid_ip());
			return;
		}

		match($subcommand){
			'ban' => $this->handleBan($sender, $ip, $args),
			'unban' => $this->handleUnban($sender, $ip),
			default => $this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_ip_invalid_subcommand())
		};
	}

	private function handleBan(CommandSender $sender, string $ip, array $args) : void{
		$duration = $args[0] ?? null;
		$reason = implode(' ', array_slice($args, 1)) ?: 'Manual ban';

		$expires = $duration ? time() + $this->parseDuration($duration) : null;
		$this->punishManager->punish($ip, PunishType::Manual, $reason, $duration ? $this->parseDuration($duration) : null);

		$expiresText = $expires ? (new \DateTime())->setTimestamp($expires)->format('Y-m-d H:i:s') : CollapseTranslationFactory::punishment_expires_never();
		$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_ip_ban_success($ip, $reason, $expiresText));
	}

	private function handleUnban(CommandSender $sender, string $ip) : void{
		$this->punishManager->removePunish($ip);
		$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_ip_unban_success($ip));
	}

	private function parseDuration(string $duration) : int{
		preg_match('/(\d+)([dhms])/', $duration, $matches);
		$num = (int) $matches[1];
		return match($matches[2] ?? 's'){
			'd' => $num * 86400,
			'h' => $num * 3600,
			'm' => $num * 60,
			default => $num
		};
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_ip_description();
	}
}
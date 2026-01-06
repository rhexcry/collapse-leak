<?php

declare(strict_types=1);

namespace collapse\command;

use collapse\cooldown\types\Command;
use collapse\cooldown\types\CooldownType;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use pocketmine\event\Listener;
use pocketmine\event\server\CommandEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\serializer\AvailableCommandsPacketAssembler;
use pocketmine\network\mcpe\protocol\serializer\AvailableCommandsPacketDisassembler;
use function explode;

final readonly class CommandListener implements Listener{

	public function __construct(
		private CommandManager $commandManager
	){}

	/**
	 * @priority LOWEST
	 */
	public function handleDataPacketSend(DataPacketSendEvent $event) : void{
		foreach($event->getTargets() as $target){
			foreach($event->getPackets() as $index => $packet){
				if($packet instanceof AvailableCommandsPacket){
					$disassembled = AvailableCommandsPacketDisassembler::disassemble($packet);
					$commandDataList = $disassembled->commandData;

					$player = $target->getPlayer();
					if(!$player instanceof CollapsePlayer || $player->getProfile() === null){
						$event->cancel();
						continue;
					}
					$translator = $player->getProfile()->getTranslator();
					$commandMap = $this->commandManager->getPlugin()->getServer()->getCommandMap();
					foreach($commandDataList as $commandData){
						$command = $commandMap->getCommand($commandData->getName());
						if(!$command instanceof CollapseCommand){
							continue;
						}
						$commandData->description = $translator->translate($command->getDescriptionForPlayer($player));
						if($command->getCommandArguments() !== null){
							$commandData->overloads = $command->getCommandArguments()->getOverloads();
						}
					}

					$packets = $event->getPackets();
					$packets[$index] = AvailableCommandsPacketAssembler::assemble($commandDataList, [], []);
					$event->setPackets($packets);
				}
			}
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handleCommand(CommandEvent $event) : void{
		$sender = $event->getSender();

		if(!($sender instanceof CollapsePlayer && !$sender->getProfile()->getRank()->isStaffRank())){
			return;
		}

		$cooldownManager = $this->commandManager->getPlugin()->getCooldownManager();
		if($cooldownManager->hasCooldown($sender, CooldownType::Command)){
			$sender->sendTranslatedMessage(CollapseTranslationFactory::command_has_cooldown());
			$event->cancel();
			return;
		}

		$command = $sender->getServer()->getCommandMap()->getCommand(explode(' ', $event->getCommand())[0]);
		if($command instanceof CollapseCommand && $command->getCooldown() === null){
			return;
		}
		$cooldownManager->addCooldown($sender, new Command(
			$command instanceof CollapseCommand ? $command->getCooldown() : CommandManager::DEFAULT_COMMAND_COOLDOWN
		));
	}
}

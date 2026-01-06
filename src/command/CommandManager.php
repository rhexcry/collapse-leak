<?php

declare(strict_types=1);

namespace collapse\command;

use collapse\command\base\PingCommand;
use collapse\command\base\ReplyCommand;
use collapse\command\base\ShowCoordinatesCommand;
use collapse\command\base\TellCommand;
use collapse\command\base\WorldCommand;
use collapse\Practice;
use pocketmine\command\defaults\GamemodeCommand;
use pocketmine\command\defaults\GarbageCollectorCommand;
use pocketmine\command\defaults\ListCommand;
use pocketmine\command\defaults\StatusCommand;
use pocketmine\command\defaults\TeleportCommand;
use pocketmine\command\defaults\TimingsCommand;

final readonly class CommandManager{

	public const float DEFAULT_COMMAND_COOLDOWN = 2.0;

	public function __construct(
		private Practice $plugin
	){
		$this->unregisterDefaultCommands();
		$this->registerBaseCommands();
		$this->plugin->getServer()->getPluginManager()->registerEvents(new CommandListener($this), $this->plugin);
	}

	public function getPlugin() : Practice{
		return $this->plugin;
	}

	private function registerBaseCommands() : void{
		$this->plugin->getServer()->getCommandMap()->registerAll('collapse', [
			new PingCommand(),
			new ReplyCommand(),
			new ShowCoordinatesCommand(),
			new TellCommand(),
			new WorldCommand(),
		]);
		$this->plugin->getServer()->getCommandMap()->registerAll('default', [
			new GarbageCollectorCommand(),
			new StatusCommand(),
			new GamemodeCommand(),
			new TeleportCommand(),
			new ListCommand(),
			new TimingsCommand(),
		]);
	}

	private function unregisterDefaultCommands() : void{
		$commandMap = $this->plugin->getServer()->getCommandMap();
		foreach($commandMap->getCommands() as $command){
			if(!$command instanceof CollapseCommand){
				$command->setLabel('__');
				$commandMap->unregister($command);
			}
		}
	}
}

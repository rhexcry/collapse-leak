<?php

declare(strict_types=1);

namespace collapse\system\restart;

use collapse\i18n\CollapseTranslationFactory;
use collapse\Practice;
use collapse\system\restart\command\RestartCommand;
use collapse\utils\TimeUtils;
use pocketmine\scheduler\Task;

final class RestartManager{

	private const int RESTART_INTERVAL = 1 * 60 * 60; // 1 hour
	private const array WARN_INTERVALS = [3600, 1800, 900, 300, 180, 60, 30, 15, 10, 5, 4, 3, 2, 1];

	private int $nextRestartTime;
	private bool $restarting = false;

	public function __construct(
		private readonly Practice $plugin
	){
		$this->nextRestartTime = time() + self::RESTART_INTERVAL;
		$this->plugin->getScheduler()->scheduleRepeatingTask(new RestartTask($this), 20);
		$this->plugin->getServer()->getCommandMap()->register('restart', new RestartCommand($this));
	}

	public function checkRestartTime() : void{
		if($this->restarting){
			return;
		}

		$timeLeft = $this->nextRestartTime - time();

		foreach(self::WARN_INTERVALS as $interval){
			if($timeLeft === $interval){
				$this->broadcastWarning($interval);
				break;
			}
		}

		if($timeLeft <= 0){
			$this->initiateRestart();
		}
	}

	private function broadcastWarning(int $seconds) : void{
		$converted = TimeUtils::convert($seconds);
		foreach(Practice::onlinePlayers() as $player){
			if($player->isConnected() && $player->getProfile()?->getTranslator() !== null){
				$player->sendTranslatedMessage(CollapseTranslationFactory::server_restart_chat($converted));
			}
		}
	}

	public function initiateRestart() : void{
		if($this->restarting){
			return;
		}

		$this->restarting = true;

		foreach(Practice::onlinePlayers() as $player){
			$player->transfer('clps.gg', 19132, $player->getProfile()->getTranslator()->translate(CollapseTranslationFactory::server_restart_kick_message()));
		}

		$this->plugin->getScheduler()->scheduleDelayedTask(new class($this->plugin) extends Task{
			public function __construct(
				private readonly Practice $plugin
			){}

			public function onRun() : void{
				$this->plugin->getServer()->shutdown();
			}
		}, 40);
	}

	public function getTimeLeft() : int{
		return max(0, $this->nextRestartTime - time());
	}

	public function forceRestart(int $delay = 10) : void{
		$this->nextRestartTime = time() + $delay;
	}
}
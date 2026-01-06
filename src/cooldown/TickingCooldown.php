<?php

declare(strict_types=1);

namespace collapse\cooldown;

use collapse\player\CollapsePlayer;
use collapse\Practice;
use pocketmine\scheduler\ClosureTask;

abstract class TickingCooldown implements Cooldown{

	private ClosureTask $task;

	public function __construct(
		protected readonly CollapsePlayer $player,
		protected readonly int $duration = 15
	){}

	abstract protected function getTicks() : int;

	final public function onStart() : void{
		Practice::getInstance()->getScheduler()->scheduleRepeatingTask($this->task = new ClosureTask(function() : void{
			if(!$this->player->isConnected()){
				$this->forceComplete();
				return;
			}
			$this->onTick();
		}), $this->getTicks());
		$this->onStartTicking();
	}

	abstract protected function onStartTicking() : void;

	final protected function forceComplete() : void{
		Practice::getInstance()->getCooldownManager()->cancel($this->player, $this->getType());
		$this->onCompletion();
	}

	final public function onCompletion() : void{
		$this->task->getHandler()?->cancel();
		$this->onCompletedTicking();
	}

	final public function isActive() : bool{
		return $this->task->getHandler() !== null;
	}

	abstract protected function onCompletedTicking() : void;

	abstract protected function onTick() : void;
}

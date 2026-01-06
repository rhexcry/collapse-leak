<?php

namespace collapse\system\internal\network;

use collapse\system\internal\InternalManager;
use pocketmine\scheduler\Task;

class ServerLagTask extends Task{

	private const int STARTING_TIME = 30;
	private const int LAST_NOTIFY = 10;

	private int $startingTime;
	private int $lastNotify;

	public function __construct(
		private readonly InternalManager $internal
	){
		$this->startingTime = time();
		$this->lastNotify = $this->startingTime;
	}

	public function onRun() : void{
		$currentFloat = microtime(true);
		$currentInt = time();

		$tickTask = ServerTickTask::getInstance();
		if($tickTask->isLagging($currentFloat) && ($currentInt - $this->startingTime) >= self::STARTING_TIME){
			if($currentInt - $this->lastNotify >= self::LAST_NOTIFY){
				$this->lastNotify = $currentInt;
				$this->internal->getInternalLogger()->onServerLag($currentFloat - $tickTask->getTick());
			}
		}
	}
}
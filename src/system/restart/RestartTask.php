<?php

declare(strict_types=1);

namespace collapse\system\restart;

use pocketmine\scheduler\Task;

final class RestartTask extends Task{

	public function __construct(
		private readonly RestartManager $manager
	){}


	public function onRun() : void{
		$this->manager->checkRestartTime();
	}
}
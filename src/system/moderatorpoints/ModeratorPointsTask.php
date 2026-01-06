<?php

declare(strict_types=1);

namespace collapse\system\moderatorpoints;

use pocketmine\scheduler\Task;

final class ModeratorPointsTask extends Task{

	public function __construct(
		private readonly ModeratorPointsManager $manager
	){}

	public function onRun() : void{
		$this->manager->checkAFK();
		$this->manager->addMpForOnlineMinutes();
	}
}
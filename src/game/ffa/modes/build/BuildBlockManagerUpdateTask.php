<?php

declare(strict_types=1);

namespace collapse\game\ffa\modes\build;

use pocketmine\scheduler\Task;

final class BuildBlockManagerUpdateTask extends Task{

	public function __construct(
		private readonly BuildBlockManager $blockManager
	){}

	public function onRun() : void{
		$this->blockManager->update();
	}
}

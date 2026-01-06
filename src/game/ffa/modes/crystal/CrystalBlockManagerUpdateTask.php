<?php

declare(strict_types=1);

namespace collapse\game\ffa\modes\crystal;

use pocketmine\scheduler\Task;

final class CrystalBlockManagerUpdateTask extends Task{

	public function __construct(
		private readonly CrystalBlockManager $blockManager
	){}

	public function onRun() : void{
		$this->blockManager->update();
	}
}

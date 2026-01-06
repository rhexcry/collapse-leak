<?php

declare(strict_types=1);

namespace collapse\system\internal\network;

use pocketmine\scheduler\Task;
use pocketmine\utils\SingletonTrait;
use function microtime;

class ServerTickTask extends Task{
	use SingletonTrait;

	private float $tick;

	public function __construct(){
		$this->tick = microtime(true);
	}

	public function onRun() : void{
		$this->tick = microtime(true);
	}

	public function getTick() : float{
		return $this->tick;
	}

	public function isLagging(float $l) : bool{
		$lsat = $l - $this->tick;
		return $lsat >= 5;
	}
}
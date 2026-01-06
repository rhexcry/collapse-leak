<?php

declare(strict_types=1);

namespace collapse\system\internal\packetlimiter;

use pocketmine\scheduler\Task;

final class PacketLimiterTask extends Task{
	public function __construct(
		private readonly PacketLimiterManager $manager
	){}

	public function onRun() : void{
		$this->manager->checkPacketLimiter();
	}
}
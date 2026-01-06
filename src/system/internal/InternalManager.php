<?php

declare(strict_types=1);

namespace collapse\system\internal;

use collapse\Practice;
use collapse\social\logger\InternalLogger;
use collapse\system\internal\packetlimiter\PacketLimiterManager;
use collapse\system\internal\punish\PunishManager;

final class InternalManager{

	private InternalLogger $internalLogger;

	private PunishManager $punishManager;

	private PacketLimiterManager $packetLimiter;

	public function __construct(private readonly Practice $plugin){
		$this->internalLogger = $this->plugin->getSocialManager()->getInternalLogger();

		$this->punishManager = new PunishManager($this);
		$this->punishManager->initIptablesChain();

		$this->packetLimiter = new PacketLimiterManager($this);
	}

	public function getInternalLogger() : InternalLogger{
		return $this->internalLogger;
	}

	public function getPunishManager() : PunishManager{
		return $this->punishManager;
	}

	public function getPacketLimiterManager() : PacketLimiterManager{
		return $this->packetLimiter;
	}
}
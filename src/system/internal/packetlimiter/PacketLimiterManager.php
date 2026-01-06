<?php

declare(strict_types=1);

namespace collapse\system\internal\packetlimiter;

use collapse\Practice;
use collapse\system\internal\InternalManager;
use collapse\system\internal\punish\PunishType;
use pocketmine\Server;

final class PacketLimiterManager{
	private const int TASK_INTERVAL = 20;

	private const int PACKET_LIMIT = 650;
	private const int BLOCK_DURATION = 180;
	private const string KICK_MESSAGE = "Too much packets";

	/** @var array<string, int> */
	private array $packetsPerSecond = [];
	/** @var array<string, int> */
	private array $consecutiveViolations = [];

	public function __construct(private readonly InternalManager $internal){
		Server::getInstance()->getPluginManager()->registerEvents(new PacketLimiterListener($this), Practice::getInstance());
		Practice::getInstance()->getScheduler()->scheduleRepeatingTask(new PacketLimiterTask($this), self::TASK_INTERVAL);
	}

	public function updateList(string $playerName) : void{
		if(!isset($this->packetsPerSecond[$playerName])){
			$this->packetsPerSecond[$playerName] = 0;
		}
		$this->packetsPerSecond[$playerName]++;
	}

	public function removeFromList(string $playerName) : void{
		if(isset($this->packetsPerSecond[$playerName])){
			unset($this->packetsPerSecond[$playerName]);
		}
		if(isset($this->consecutiveViolations[$playerName])){
			unset($this->consecutiveViolations[$playerName]);
		}
	}

	public function checkPacketLimiter() : void{
		foreach(Practice::onlinePlayers() as $player){
			if(!$player->isConnected()){
				continue;
			}

			$playerName = $player->getName();
			$packetsPerSecond = $this->packetsPerSecond[$playerName] ?? 0;
			$violations = $this->consecutiveViolations[$playerName] ?? 0;
			if($packetsPerSecond > self::PACKET_LIMIT){
				$violations++;
				$this->consecutiveViolations[$playerName] = $violations;
				if($violations >= 3){
					$player->kick(self::KICK_MESSAGE);
					$this->internal->getPunishManager()->punish(
						$player->getNetworkSession()->getIp(),
						PunishType::PacketSpam,
						self::KICK_MESSAGE,
						self::BLOCK_DURATION
					);
					unset($this->packetsPerSecond[$playerName]);
					unset($this->consecutiveViolations[$playerName]);
				}
			} else {
				$this->consecutiveViolations[$playerName] = 0;
			}
			$this->packetsPerSecond[$playerName] = 0;
		}
	}
}
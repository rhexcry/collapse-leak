<?php

declare(strict_types=1);

namespace collapse\system\internal\punish;

use collapse\mongo\operation\DeleteManyOperation;
use collapse\Practice;
use collapse\social\logger\InternalLogger;
use collapse\mongo\MongoWrapper;
use collapse\mongo\operation\InsertOneOperation;
use collapse\system\internal\InternalManager;
use collapse\system\internal\punish\command\IpCommand;
use MongoDB\Collection;
use pocketmine\Server;
use pocketmine\utils\Utils;

final class PunishManager{
	private const string COLLECTION = 'internal_punishments';
	private const string IPTABLES_CHAIN = 'blocked_ips';
	private const string DEV_NULL_PREFIX = '2>/dev/null || true';

	private readonly InternalLogger $logger;

	private readonly Collection $collection;

	public function __construct(private readonly InternalManager $internal){
		$this->logger = $this->internal->getInternalLogger();
		$this->collection = MongoWrapper::getClient()->selectCollection(Practice::getDatabaseName(), self::COLLECTION);
		if(Utils::getOS() === Utils::OS_LINUX && !Practice::isTestServer()){
			Server::getInstance()->getCommandMap()->register('collapse', new IpCommand($this));
			Practice::getInstance()->getScheduler()->scheduleRepeatingTask(new PunishTask($this), 1200);
		}
	}

	public function initIptablesChain() : void{
		if(Utils::getOS() === Utils::OS_LINUX && !Practice::isTestServer()){
			shell_exec("iptables -N " . self::IPTABLES_CHAIN . " " . self::DEV_NULL_PREFIX);
			shell_exec("iptables -D INPUT -p udp -j " . self::IPTABLES_CHAIN . " " . self::DEV_NULL_PREFIX);
			shell_exec("iptables -A INPUT -p udp -j " . self::IPTABLES_CHAIN . " " . self::DEV_NULL_PREFIX);
		}
	}

	public function punish(string $ip, PunishType $type, ?string $reason = null, ?int $duration = null) : void{
		$this->removeIptablesRule($ip);
		$data = [
			'type' => $type->value,
			'ip' => $ip,
			'reason' => $reason ?? 'Automated',
			'timestamp' => time(),
			'expires_at' => $duration !== null ? time() + $duration : null
		];
		MongoWrapper::push(new InsertOneOperation(
			$this->collection->getDatabaseName(),
			$this->collection->getCollectionName(),
			$data
		));
		shell_exec("iptables -A " . self::IPTABLES_CHAIN . " -s " . escapeshellarg($ip) . " -p udp -j DROP " . self::DEV_NULL_PREFIX);
		$this->logger->onIpBlock($ip, $type, $reason);
	}

	public function removeIptablesRule(string $ip) : void{
		shell_exec("iptables -D " . self::IPTABLES_CHAIN . " -s " . escapeshellarg($ip) . " -p udp -j DROP " . self::DEV_NULL_PREFIX);
	}

	public function removePunish(string $ip) : void{
		$this->removeIptablesRule($ip);
		$this->logger->onIpUnblock($ip, "manual");
		MongoWrapper::push(new DeleteManyOperation(
			$this->collection->getDatabaseName(),
			$this->collection->getCollectionName(),
			['ip' => $ip]
		));
	}

	public function clearBlocks() : void{
		shell_exec("iptables -F " . self::IPTABLES_CHAIN . " " . self::DEV_NULL_PREFIX);
		MongoWrapper::push(new DeleteManyOperation(
			$this->collection->getDatabaseName(),
			$this->collection->getCollectionName(),
			[]
		));
	}

	public function getCollection() : Collection{
		return $this->collection;
	}

	public function getLogger() : InternalLogger{
		return $this->logger;
	}
}
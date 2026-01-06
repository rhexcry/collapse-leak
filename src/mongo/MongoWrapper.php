<?php

declare(strict_types=1);

namespace collapse\mongo;

use collapse\mongo\operation\MongoOperation;
use collapse\mongo\promise\MongoPromise;
use MongoDB\Client;
use pmmp\thread\Thread as NativeThread;
use pmmp\thread\ThreadSafeArray;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use const COLLAPSE_SERVER_PATH;

final class MongoWrapper{
	use SingletonTrait;

	private const string MONGO_CLIENT_SETTINGS = COLLAPSE_SERVER_PATH . 'mongo.json';

	private static ?Client $client = null;

	private ThreadSafeArray $outgoingQueue;

	private MongoThread $thread;

	private int $sleeperHandlerEntryId;

	private int $promiseId = 0;

	/** @var MongoPromise[] */
	private array $promises = [];

	public static function getClient() : Client{
		if(self::$client === null){
			$config = new Config(self::MONGO_CLIENT_SETTINGS, Config::JSON);
			\GlobalLogger::get()->notice('Initializing new MongoClient');
			return self::$client = new Client($config->get('uri'), $config->get('options', []));
		}
		return self::$client;
	}

	public function __construct(){
		$this->outgoingQueue = new ThreadSafeArray();
		$server = Server::getInstance();
		$sleeperHandlerEntry = $server->getTickSleeper()->addNotifier(function() : void{
			while($this->outgoingQueue->count() > 0){ //???
				foreach($this->outgoingQueue as $promise => $result){
					$this->promises[$promise]->resolve($result);
					unset($this->outgoingQueue[$promise], $this->promises[$promise]);
				}
			}
		});
		$this->sleeperHandlerEntryId = $sleeperHandlerEntry->getNotifierId();
		$this->thread = new MongoThread($this->outgoingQueue, $sleeperHandlerEntry, $server->getLogger());
		$this->thread->start(NativeThread::INHERIT_CONSTANTS);
	}

	public static function push(MongoOperation $operation) : MongoPromise{
		return MongoWrapper::getInstance()->pushOperation($operation);
	}

	/**
	 * @internal
	 */
	private function pushOperation(MongoOperation $operation) : MongoPromise{
		$promise = new MongoPromise();
		$this->promises[$operation->promiseId = $this->promiseId++] = $promise;
		$this->thread->push($operation);
		$this->thread->notify();
		return $promise;
	}

	public function close() : void{
		Server::getInstance()->getTickSleeper()->removeNotifier($this->sleeperHandlerEntryId);
	}
}

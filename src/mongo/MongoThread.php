<?php

declare(strict_types=1);

namespace collapse\mongo;

use collapse\mongo\operation\MongoOperation;
use pmmp\thread\ThreadSafeArray;
use pocketmine\snooze\SleeperHandlerEntry;
use pocketmine\thread\NonThreadSafeValue;
use pocketmine\thread\Thread;
use pocketmine\utils\MainLogger;
use const COLLAPSE_AUTOLOADER_PATH;

final class MongoThread extends Thread{

	/** @var ThreadSafeArray<NonThreadSafeValue<MongoOperation>> */
	private ThreadSafeArray $ingoingQueue;

	public function __construct(
		private ThreadSafeArray $outgoingQueue,
		private readonly SleeperHandlerEntry $sleeperHandlerEntry,
		private readonly MainLogger $logger
	){
		$this->ingoingQueue = new ThreadSafeArray();
	}

	public function push(MongoOperation $operation) : void{
		$this->ingoingQueue[] = new NonThreadSafeValue($operation);
	}

	public function onRun() : void{
		\GlobalLogger::set($this->logger);
		require_once(COLLAPSE_AUTOLOADER_PATH);
		$notifier = $this->sleeperHandlerEntry->createNotifier();
		$client = MongoWrapper::getClient();

		while(!$this->isKilled || $this->ingoingQueue->count() > 0){
			try{
				/** @var NonThreadSafeValue $serialized */
				if(($serialized = $this->ingoingQueue->shift()) !== null){
					/** @var MongoOperation $operation */
					$operation = $serialized->deserialize();
					$this->outgoingQueue[$operation->promiseId] = $operation->work($client);
					$notifier->wakeupSleeper();
				}
			}catch(\Throwable $exception){
				$this->logger->logException($exception);
			}

			if($this->ingoingQueue->count() === 0){
				$this->synchronized(function() : void{
					$this->wait();
				});
			}
		}
	}

	public function getThreadName() : string{
		return 'Mongo';
	}
}

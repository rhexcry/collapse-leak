<?php

declare(strict_types=1);

namespace collapse\social;

use collapse\social\request\SocialRequest;
use pmmp\thread\Thread as NativeThread;
use pmmp\thread\ThreadSafeArray;
use pocketmine\thread\log\AttachableThreadSafeLogger;
use pocketmine\thread\NonThreadSafeValue;
use pocketmine\thread\Thread;
use const COLLAPSE_AUTOLOADER_PATH;

abstract class SocialThread extends Thread{

	/** @var ThreadSafeArray<NonThreadSafeValue<SocialRequest>> */
	protected ThreadSafeArray $requestQueue;

	public function __construct(private readonly AttachableThreadSafeLogger $logger){
		$this->requestQueue = new ThreadSafeArray();
		$this->start(NativeThread::INHERIT_CONSTANTS);
	}

	protected function onInitialize() : void{}

	public function request(SocialRequest $request) : void{
		$this->requestQueue[] = new NonThreadSafeValue($request);
		$this->synchronized(function() : void{
			$this->notify();
		});
	}

	public function onRun() : void{
		require_once(COLLAPSE_AUTOLOADER_PATH);

		while(!$this->isKilled || $this->requestQueue->count() > 0){
			/** @var NonThreadSafeValue<SocialRequest> $serialized */
			if(($serialized = $this->requestQueue->shift()) !== null){
				try{
					$serialized->deserialize()->execute();
				}catch(\Throwable $e){
					$this->logger->logException($e);
				}
			}

			if($this->requestQueue->count() === 0){
				$this->synchronized(function() : void{
					$this->wait();
				});
			}
		}
	}

	public function getThreadName() : string{
		return 'Social';
	}
}

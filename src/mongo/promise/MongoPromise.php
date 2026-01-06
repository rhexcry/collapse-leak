<?php

declare(strict_types=1);

namespace collapse\mongo\promise;

use pocketmine\thread\NonThreadSafeValue;

final class MongoPromise{

	/** @var (\Closure(mixed $result) : void)[] */
	private array $callbacks = [];

	private mixed $result = null;

	public function onResolve(\Closure $callback) : void{
		if($this->result === null){
			$this->callbacks[] = $callback;
		}else{
			foreach($this->callbacks as $callback){
				$callback($this->result);
			}
		}
	}

	public function resolve(mixed $result) : void{
		if($result instanceof NonThreadSafeValue){
			$result = $result->deserialize(); //Hack
		}
		$this->result = $result;

		foreach($this->callbacks as $callback){
			$callback($result);
		}
	}
}

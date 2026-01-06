<?php

declare(strict_types=1);

namespace collapse\game\duel\promise;

use collapse\game\duel\Duel;

final class DuelCreationPromise{

	/** @var (\Closure(Duel $duel) : void)[] */
	private array $callbacks = [];

	private ?Duel $duel = null;

	public function onCreate(\Closure $callback) : void{
		$this->callbacks[] = $callback;
		if($this->duel !== null){
			foreach($this->callbacks as $callback){
				$callback($this->duel);
			}
		}
	}

	public function resolve(Duel $duel) : void{
		if($this->duel !== null){
			throw new \InvalidArgumentException('Promise already resolved');
		}
		$this->duel = $duel;
		foreach($this->callbacks as $callback){
			$callback($this->duel);
		}
	}
}

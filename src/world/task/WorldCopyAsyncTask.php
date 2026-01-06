<?php

declare(strict_types=1);

namespace collapse\world\task;

use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\Filesystem;

final class WorldCopyAsyncTask extends AsyncTask{

	private const string TLS_KEY_COMPLETION = 'completion';

	public function __construct(
		private readonly string $origin,
		private readonly string $destination,
		\Closure $onCompletion
	){
		$this->storeLocal(self::TLS_KEY_COMPLETION, $onCompletion);
	}

	public function onRun() : void{
		Filesystem::recursiveCopy($this->origin, $this->destination);
	}

	public function onCompletion() : void{
		($this->fetchLocal(self::TLS_KEY_COMPLETION))();
	}
}

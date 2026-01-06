<?php

declare(strict_types=1);

namespace collapse\world\task;

use pocketmine\scheduler\AsyncTask;
use pocketmine\utils\Filesystem;

final class WorldDeleteAsyncTask extends AsyncTask{

	private const string TLS_KEY_COMPLETION = 'completion';

	public function __construct(
		private readonly string $dir,
		\Closure $onCompletion
	){
		$this->storeLocal(self::TLS_KEY_COMPLETION, $onCompletion);
	}

	public function onRun() : void{
		Filesystem::recursiveUnlink($this->dir);
	}

	public function onCompletion() : void{
		($this->fetchLocal(self::TLS_KEY_COMPLETION))();
	}
}

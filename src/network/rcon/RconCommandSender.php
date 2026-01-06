<?php

declare(strict_types=1);

namespace collapse\network\rcon;

use pocketmine\console\ConsoleCommandSender;
use pocketmine\lang\Translatable;
use function trim;

class RconCommandSender extends ConsoleCommandSender{

	private string $messages = '';

	private ?string $name = null;

	public function sendMessage(Translatable|string $message) : void{
		if($message instanceof Translatable){
			$message = $this->getServer()->getLanguage()->translate($message);
		}

		$this->messages .= trim($message, "\r\n") . "\n";
	}

	public function getMessage() : string{
		return $this->messages;
	}

	public function setName(string $name) : void{
		$this->name = $name;
	}

	public function getName() : string{
		return $this->name ?? 'Rcon';
	}
}

<?php

declare(strict_types=1);

namespace collapse\social\entry;

use collapse\social\request\telegram\TelegramMessage;
use collapse\social\SocialThread;

final class Telegram extends SocialThread{

	public const string URL = 'https://api.telegram.org/bot';

	public function sendMessage(TelegramMessage $message) : void{
		$this->request($message);
	}
}

<?php

declare(strict_types=1);

namespace collapse\social\logger;

use collapse\social\request\telegram\TelegramMessage;
use collapse\social\SocialEnvKeys;

final class TelegramStaffMessage extends TelegramMessage{

	public function __construct(string $message){
		parent::__construct(
			$_ENV[SocialEnvKeys::ENV_TELEGRAM_STAFF_TOKEN],
			(int) $_ENV[SocialEnvKeys::ENV_TELEGRAM_STAFF_CHAT_ID],
			$message,
			(int) $_ENV[SocialEnvKeys::ENV_TELEGRAM_STAFF_TOPIC_ID]
		);
	}
}

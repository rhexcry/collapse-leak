<?php

declare(strict_types=1);

namespace collapse\social;

final readonly class SocialEnvKeys{

	public const string ENV_TELEGRAM_STAFF_TOKEN = 'TELEGRAM_STAFF_TOKEN';
	public const string ENV_TELEGRAM_STAFF_CHAT_ID = 'TELEGRAM_STAFF_CHAT_ID';
	public const string ENV_TELEGRAM_STAFF_TOPIC_ID = 'TELEGRAM_STAFF_TOPIC_ID';

	public const string ENV_TELEGRAM_REPORT_TOKEN = 'TELEGRAM_REPORT_TOKEN';
	public const string ENV_TELEGRAM_REPORT_CHAT_ID = 'TELEGRAM_REPORT_CHAT_ID';
	public const string ENV_TELEGRAM_REPORT_TOPIC_ID = 'TELEGRAM_REPORT_TOPIC_ID';

	public const string ENV_TELEGRAM_INTERNAL_TOKEN = 'TELEGRAM_INTERNAL_TOKEN';
	public const string ENV_TELEGRAM_INTERNAL_CHAT_ID = 'TELEGRAM_INTERNAL_CHAT_ID';
	public const string ENV_TELEGRAM_INTERNAL_TOPIC_ID = 'TELEGRAM_INTERNAL_TOPIC_ID';

	private function __construct(){}
}
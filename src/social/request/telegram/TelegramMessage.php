<?php

declare(strict_types=1);

namespace collapse\social\request\telegram;

use collapse\social\entry\Telegram;
use collapse\social\request\SocialRequest;
use collapse\social\SocialException;
use pocketmine\utils\Internet;
use function json_decode;
use function sprintf;

class TelegramMessage extends SocialRequest{

	private const string URL = Telegram::URL . '%s/sendMessage';

	public function __construct(
		#[\SensitiveParameter] private readonly string $token,
		private readonly int $chat,
		private readonly string $message,
		private readonly ?int $topic = null
	){}

	public function execute() : void{
		$params = [
			'chat_id' => $this->chat,
			'text' => $this->message,
			'parse_mode' => 'MarkdownV2',
		];
		if($this->topic !== null){
			$params['message_thread_id'] = $this->topic;
		}
		$response = Internet::postURL(sprintf(TelegramMessage::URL, $this->token), $params, err: $error);
		if($response !== null){
			$body = json_decode($response->getBody(), true);
			if($body['ok'] !== true){
				throw new SocialException($body['description']);
			}
		}
		if($response === false){
			if($error === null){
				throw new SocialException('Request failed without error');
			}else{
				throw new SocialException($error);
			}
		}
	}
}

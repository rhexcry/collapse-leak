<?php

declare(strict_types=1);

namespace collapse\system\broadcast;

use collapse\i18n\CollapseTranslationFactory;
use collapse\Practice;
use pocketmine\lang\Translatable;
use pocketmine\scheduler\Task;

final class BroadcastTask extends Task{

	public const int DELAY = 20 * 60 * 5; // 5 minutes

	private int $index = 0;

	/** @var Translatable[] */
	private readonly array $messages;

	public function __construct(){
		$this->messages = [
			CollapseTranslationFactory::broadcast_social_telegram(),
			CollapseTranslationFactory::broadcast_social_discord(),
			CollapseTranslationFactory::broadcast_social_vk(),
			CollapseTranslationFactory::broadcast_social_site()
		];
	}

	public function onRun() : void{
		if(empty($this->messages)){
			return;
		}

		$message = $this->messages[$this->index];
		foreach(Practice::onlinePlayers() as $player){
			if(!$player->isConnected() or $player->getProfile() === null){
				continue;
			}

			$player->sendTranslatedMessage($message);
		}

		$this->index = ($this->index + 1) % count($this->messages);
	}
}
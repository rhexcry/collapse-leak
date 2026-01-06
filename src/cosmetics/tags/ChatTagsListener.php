<?php

declare(strict_types=1);

namespace collapse\cosmetics\tags;

use collapse\player\profile\event\ProfileLoadedEvent;
use pocketmine\event\Listener;

final readonly class ChatTagsListener implements Listener{

	public function __construct(
		private ChatTagsManager $chatTagsManager
	){}

	/**
	 * @priority LOWEST
	 */
	public function handleProfileLoaded(ProfileLoadedEvent $event) : void{
		$profile = $event->getProfile();
		if($profile->getChatTag() === null){
			return;
		}
		$this->chatTagsManager->setChatTag($profile, $profile->getChatTag());
	}
}

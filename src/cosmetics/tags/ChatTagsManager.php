<?php

declare(strict_types=1);

namespace collapse\cosmetics\tags;

use collapse\player\profile\Profile;
use collapse\Practice;

final readonly class ChatTagsManager{

	public function __construct(
		private Practice $plugin
	){
		$this->plugin->getServer()->getPluginManager()->registerEvents(new ChatTagsListener($this), $this->plugin);
	}

	public function getPlugin() : Practice{
		return $this->plugin;
	}

	public function onChangeChatTag(Profile $profile, ?ChatTag $chatTag) : void{
		$profile->setChatTag($chatTag);
		$profile->save();
		$this->setChatTag($profile, $chatTag);
	}

	public function setChatTag(Profile $profile, ?ChatTag $chatTag) : void{
		$player = $profile->getPlayer();
		if($player === null){
			return;
		}
		$player->setScoreTag($chatTag?->toDisplayName() ?? '');
	}
}

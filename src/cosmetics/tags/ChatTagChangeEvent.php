<?php

declare(strict_types=1);

namespace collapse\cosmetics\tags;

use collapse\player\profile\event\ProfileEvent;
use collapse\player\profile\Profile;

final class ChatTagChangeEvent extends ProfileEvent{

	public function __construct(
		Profile $profile,
		private readonly ?ChatTag $chatTag
	){
		parent::__construct($profile);
	}

	public function getChatTag() : ?ChatTag{
		return $this->chatTag;
	}
}

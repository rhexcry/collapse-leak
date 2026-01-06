<?php

declare(strict_types=1);

namespace collapse\cosmetics\capes;

use collapse\player\profile\event\ProfileEvent;
use collapse\player\profile\Profile;

final class CapeChangeEvent extends ProfileEvent{

	public function __construct(
		Profile $profile,
		private readonly ?Cape $cape
	){
		parent::__construct($profile);
	}

	public function getCape() : ?Cape{
		return $this->cape;
	}
}

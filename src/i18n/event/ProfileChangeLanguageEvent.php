<?php

declare(strict_types=1);

namespace collapse\i18n\event;

use collapse\i18n\types\LanguageInterface;
use collapse\player\profile\event\ProfileEvent;
use collapse\player\profile\Profile;

final class ProfileChangeLanguageEvent extends ProfileEvent{

	public function __construct(
		Profile $profile,
		private readonly LanguageInterface $language
	){
		parent::__construct($profile);
	}

	public function getLanguage() : LanguageInterface{
		return $this->language;
	}
}

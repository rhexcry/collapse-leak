<?php

declare(strict_types=1);

namespace collapse\system\friend\request;

use collapse\i18n\CollapseTranslationFactory;
use pocketmine\lang\Translatable;
use pocketmine\utils\TextFormat;

enum FriendRequestStatus : string{

	case Pending = 'pending';
	case Accepted = 'accepted';
	case Declined = 'declined';

	public function getColor() : string{
		return match($this){
			self::Pending => TextFormat::YELLOW,
			self::Accepted => TextFormat::GREEN,
			self::Declined => TextFormat::RED
		};
	}

	public function getDisplayName() : Translatable{
		return match($this){
			self::Pending => CollapseTranslationFactory::friends_status_pending(),
			self::Accepted => CollapseTranslationFactory::friends_status_accepted(),
			self::Declined => CollapseTranslationFactory::friends_status_declined()
		};
	}
}

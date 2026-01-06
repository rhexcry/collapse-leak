<?php

declare(strict_types=1);

namespace collapse\cosmetics\potion;

use collapse\i18n\CollapseTranslationFactory;
use collapse\player\profile\Profile;
use collapse\player\rank\Rank;
use pocketmine\color\Color;
use pocketmine\lang\Translatable;

enum PotionColor : string{
	case Blue = 'blue';
	case Yellow = 'yellow';
	case Black = 'black';
	case Pink = 'pink';
	case Green = 'green';
	case White = 'white';

	public function toDisplayName() : Translatable{
		return match($this){
			self::Blue => CollapseTranslationFactory::potion_color_blue(),
			self::Yellow => CollapseTranslationFactory::potion_color_yellow(),
			self::Black => CollapseTranslationFactory::potion_color_black(),
			self::Pink => CollapseTranslationFactory::potion_color_pink(),
			self::Green => CollapseTranslationFactory::potion_color_green(),
			self::White => CollapseTranslationFactory::potion_color_white()
		};
	}

	public function canUse(Profile $profile) : bool{
		return $profile->getRank()->getPriority() >= Rank::LUMINOUS->getPriority();
	}

	/**
	 * @return Color[]
	 */
	public function toColors() : array{
		return match($this){
			self::Blue => [new Color(0, 0, 255)],
			self::Yellow => [new Color(255, 255, 0)],
			self::Black => [new Color(0, 0, 0)],
			self::Pink => [new Color(255, 105, 180)],
			self::Green => [new Color(0, 255, 0)],
			self::White => [new Color(255, 255, 255)]
		};
	}
}
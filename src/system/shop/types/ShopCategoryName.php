<?php

declare(strict_types=1);

namespace collapse\system\shop\types;

enum ShopCategoryName : string{
	case Ranks = 'ranks';
	case ChatTags = 'chat_tags';
	case Capes = 'capes';
	case DeathEffects = 'death_effects';
	case PotionColors = 'potion_colors';
}

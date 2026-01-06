<?php

declare(strict_types=1);

namespace collapse\feature\reward;

enum RewardType : string{

	case Valute = 'valute';
	case Game_Experience = 'experience';
	case Default_Experience = 'default_experience';
	case Default = 'default';
}

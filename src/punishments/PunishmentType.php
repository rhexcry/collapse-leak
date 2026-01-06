<?php

declare(strict_types=1);

namespace collapse\punishments;

enum PunishmentType : string{

	case Ban = 'ban';

	case Mute = 'mute';

	case Kick = 'kick';
}

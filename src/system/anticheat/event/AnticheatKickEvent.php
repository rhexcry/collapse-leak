<?php

declare(strict_types=1);

namespace collapse\system\anticheat\event;

use pocketmine\event\CancellableTrait;

final class AnticheatKickEvent extends AnticheatEvent{
	use CancellableTrait;

}
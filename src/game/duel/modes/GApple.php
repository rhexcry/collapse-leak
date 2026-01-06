<?php

declare(strict_types=1);

namespace collapse\game\duel\modes;

use collapse\game\duel\Duel;
use collapse\player\CollapsePlayer;

final class GApple extends Duel{
	public function onPlayerJoin(CollapsePlayer $player, \Closure $callback): void{}
}

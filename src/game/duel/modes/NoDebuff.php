<?php

declare(strict_types=1);

namespace collapse\game\duel\modes;

use collapse\game\duel\Duel;
use collapse\player\CollapsePlayer;

final class NoDebuff extends Duel{
	public function onPlayerJoin(CollapsePlayer $player, \Closure $callback): void{}

	public function isEnderPearlCooldown() : bool{
		return true;
	}
}

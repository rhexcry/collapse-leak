<?php

declare(strict_types=1);

namespace collapse\game\duel\modes;

use collapse\game\duel\Duel;
use collapse\player\CollapsePlayer;

final class BuildUHC extends Duel{

	public function isBlocksActions() : bool{
		return true;
	}

	public function onPlayerJoin(CollapsePlayer $player, \Closure $callback): void{}
}

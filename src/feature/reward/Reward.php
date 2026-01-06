<?php

declare(strict_types=1);

namespace collapse\feature\reward;

use collapse\player\CollapsePlayer;

interface Reward{

	public function apply(CollapsePlayer $player) : void;
}

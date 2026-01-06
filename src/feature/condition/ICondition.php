<?php

declare(strict_types=1);

namespace collapse\feature\condition;

use collapse\player\CollapsePlayer;

interface ICondition{

	public function isMet(CollapsePlayer $player, mixed $data) : bool;
}

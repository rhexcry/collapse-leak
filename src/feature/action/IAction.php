<?php

declare(strict_types=1);

namespace collapse\feature\action;

use collapse\player\CollapsePlayer;

interface IAction{

	public function execute(CollapsePlayer $player) : void;
}

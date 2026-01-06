<?php

declare(strict_types=1);

namespace collapse\item;

use collapse\player\CollapsePlayer;

interface TranslatableItem{

	public function translate(CollapsePlayer $player) : self;

}

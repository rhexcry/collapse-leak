<?php

declare(strict_types=1);

namespace collapse\feature\concrete\quest\condition;

use collapse\feature\condition\ICondition;
use collapse\game\ffa\types\FreeForAllMode;
use collapse\player\CollapsePlayer;

final readonly class FreeForAllGamemodeCondition implements ICondition{

	public function __construct(private FreeForAllMode $mode){}

	public function isMet(CollapsePlayer $player, mixed $data) : bool{
		return $data["mode"] === $this->mode;
	}
}

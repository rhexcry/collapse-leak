<?php

declare(strict_types=1);

namespace collapse\feature\concrete\quest\condition;

use collapse\feature\condition\ICondition;
use collapse\game\duel\types\DuelMode;
use collapse\player\CollapsePlayer;

final readonly class DuelGamemodeCondition implements ICondition{

	public function __construct(private DuelMode $mode){}

	public function isMet(CollapsePlayer $player, mixed $data) : bool{
		return $data["mode"] === $this->mode;
	}
}

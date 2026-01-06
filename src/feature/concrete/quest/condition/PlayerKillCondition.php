<?php

declare(strict_types=1);

namespace collapse\feature\concrete\quest\condition;

use collapse\feature\condition\ICondition;
use collapse\player\CollapsePlayer;

final readonly class PlayerKillCondition implements ICondition{

	public function __construct(private int $needKills){}

	public function isMet(CollapsePlayer $player, mixed $data) : bool{
		return $data['currentKills'] >= $this->needKills;
	}
}

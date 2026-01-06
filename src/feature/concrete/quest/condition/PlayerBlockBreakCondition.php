<?php

declare(strict_types=1);

namespace collapse\feature\concrete\quest\condition;

use collapse\feature\condition\ICondition;
use collapse\player\CollapsePlayer;

final readonly class PlayerBlockBreakCondition implements ICondition{

	public function __construct(private int $identifier, private int $needBlocks){
	}

	public function isMet(CollapsePlayer $player, mixed $data) : bool{
		return $data['currentBreak'] >= $this->needBlocks &&
			($this->identifier === -1 || $data['block']->getTypeId() === $this->identifier);
	}
}

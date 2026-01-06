<?php

declare(strict_types=1);

namespace collapse\feature\concrete\quest\condition;

use collapse\feature\condition\ICondition;
use collapse\player\CollapsePlayer;

final readonly class ItemUsageCondition implements ICondition{

	public function __construct(private int $identifier, private int $needUsage){}

	public function isMet(CollapsePlayer $player, mixed $data) : bool{
		return $data['itemId'] === $this->identifier && ($data['used']?->get($player, 0) ?? 0) >= $this->needUsage;
	}
}

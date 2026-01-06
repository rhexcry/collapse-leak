<?php

declare(strict_types=1);

namespace collapse\feature\condition;

use collapse\player\CollapsePlayer;
use function array_map;
use function in_array;

final readonly class ConditionComposite implements ICondition{

	public function __construct(
		private array $conditions,
		private ConditionLogicType $logic = ConditionLogicType::And
	){
	}

	public function isMet(CollapsePlayer $player, mixed $data) : bool{
		$results = array_map(
			fn($cond) => $cond->isMet($player, $data),
			$this->conditions
		);

		return match($this->logic){
			ConditionLogicType::And => !in_array(false, $results, true),
			ConditionLogicType::Or => in_array(true, $results, true)
		};
	}
}

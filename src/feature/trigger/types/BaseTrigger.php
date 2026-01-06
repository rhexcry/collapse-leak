<?php

declare(strict_types=1);

namespace collapse\feature\trigger\types;

use collapse\feature\action\IAction;
use collapse\feature\condition\ICondition;
use collapse\player\CollapsePlayer;

abstract class BaseTrigger implements ITrigger{

	public function __construct(
		protected readonly string $id,
		protected array $conditions,
		protected array $actions
	){
	}

	public function getId() : string{
		return $this->id;
	}

	/**
	 * @return ICondition[]
	 */
	public function getConditions() : array{
		return $this->conditions;
	}

	/**
	 * @return IAction[]
	 */
	public function getActions() : array{
		return $this->actions;
	}

	public function executeActionsFor(CollapsePlayer $player) : void{
		foreach($this->actions as $action){
			$action->execute($player);
		}

	}
}

<?php

declare(strict_types=1);

namespace collapse\feature\trigger;

use collapse\feature\trigger\types\BaseTrigger;
use collapse\feature\trigger\types\EventTrigger;
use collapse\feature\trigger\types\ITrigger;
use collapse\Practice;

final class TriggerManager{

	/** @var array<string, ITrigger> */
	private array $triggers = [];

	public function __construct(
		private Practice $plugin,
	){
	}

	public function register(ITrigger $trigger) : void{
		$this->triggers[$trigger->getId()] = $trigger;

		if($trigger instanceof BaseTrigger){
			/** @noinspection PhpParamsInspection */
			$this->plugin->getFeatureManager()->getContext()->getEventBus()->registerTriggerEvents($trigger);
		}
	}

	public function registerMultiple(array $triggers) : void{
		foreach($triggers as $trigger){
			$this->register($trigger);
		}
	}

	private function handleEvent(object $event) : void{
		foreach($this->triggers as $trigger){
			if($trigger instanceof EventTrigger && $trigger->shouldHandle($event)){
				$trigger->execute($event);
			}
		}
	}
}

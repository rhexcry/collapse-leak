<?php

declare(strict_types=1);

namespace collapse\feature;

use collapse\feature\trigger\TriggerManager;
use pocketmine\plugin\PluginBase;

final readonly class FeatureContext{

	public function __construct(
		private PluginBase $plugin,
		private EventBus $eventBus,
		private ?TriggerManager $triggerManager = null

	){
	}

	public function getEventBus() : EventBus{
		return $this->eventBus;
	}

	public function getPlugin() : PluginBase{
		return $this->plugin;
	}

	public function getTriggerManager() : ?TriggerManager{
		return $this->triggerManager;
	}
}

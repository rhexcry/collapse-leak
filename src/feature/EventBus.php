<?php

declare(strict_types=1);

namespace collapse\feature;

use collapse\feature\trigger\types\EventTrigger;
use collapse\game\event\BlockPlaceGameEvent;
use collapse\game\event\PlayerKillPlayerGameEvent;
use collapse\Practice;
use pocketmine\event\Event;
use pocketmine\event\EventPriority;
use pocketmine\Server;
use ReflectionClass;
use function count;
use function usort;

final class EventBus{

	/** @var array<class-string, array<array{callable, int}>> */
	private array $listeners = [];

	/** @var array<class-string, EventTrigger[]> */
	private array $triggers = [];

	private const array DISPATCHABLE_EVENTS = [
		PlayerKillPlayerGameEvent::class,
		BlockPlaceGameEvent::class
	];

	public function subscribeFeature(IFeature $feature) : void{
		$reflection = new ReflectionClass($feature);

		foreach($reflection->getMethods() as $method){
			$attributes = $method->getAttributes(EventSubscribe::class);

			if(count($attributes) === 0){
				continue;
			}

			foreach($attributes as $attribute){
				$eventSubscribe = $attribute->newInstance();
				$eventClass = $eventSubscribe->eventClass;
				$priority = $eventSubscribe->priority;

				$this->listeners[$eventClass][] = [
					[$feature, $method->getName()],
					$priority
				];
			}
		}
	}

	public function registerTriggerEvents(EventTrigger $trigger) : void{
		foreach($trigger->getHandleableEvents() as $eventClass){
			$this->triggers[$eventClass][] = $trigger;
		}
	}

	public function registerMultipleTriggers(array $triggers) : void{
		foreach($triggers as $trigger){
			if($trigger instanceof EventTrigger){
				$this->registerTriggerEvents($trigger);
			}
		}
	}

	public function dispatch(object $event) : void{
		$listeners = $this->listeners[$event::class] ?? [];
		usort($listeners, fn($a, $b) => $b[1] <=> $a[1]);
		foreach($listeners as [$handler, $priority]){
			$handler($event);
		}

		$triggers = $this->triggers[$event::class] ?? [];
		foreach($triggers as $trigger){
			if($trigger->shouldHandle($event)){
				$trigger->execute($event);
			}
		}
	}

	public function hook() : self{
		foreach(self::DISPATCHABLE_EVENTS as $eventClass){
			Server::getInstance()->getPluginManager()->registerEvent($eventClass, function(Event $event) : void{
				$this->dispatch($event);
			}, EventPriority::MONITOR, Practice::getInstance());
		}

		return $this;
	}
}

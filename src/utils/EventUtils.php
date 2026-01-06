<?php

declare(strict_types=1);

namespace collapse\utils;

use collapse\Practice;
use pocketmine\event\Listener;

final class EventUtils{

	private static array $registeredListeners = [];

	public static function registerListenerOnce(Listener $listener) : void{
		if(isset(self::$registeredListeners[$listener::class])){
			return;
		}
		$plugin = Practice::getInstance();
		$plugin->getServer()->getPluginManager()->registerEvents($listener, $plugin);
		self::$registeredListeners[$listener::class] = true;
	}

	private function __construct(){}
}

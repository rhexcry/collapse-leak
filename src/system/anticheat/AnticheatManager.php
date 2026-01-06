<?php

declare(strict_types=1);

namespace collapse\system\anticheat;

use collapse\Practice;
use collapse\system\anticheat\check\Check;
use collapse\system\anticheat\check\combat\reach\ReachA;
use collapse\system\anticheat\check\combat\reach\ReachB;
use collapse\system\anticheat\check\combat\reach\ReachC;
use collapse\system\anticheat\check\combat\reach\ReachD;
use collapse\system\anticheat\check\fly\FlyA;
use collapse\system\anticheat\check\fly\FlyB;

final class AnticheatManager{

	/** @var array<string, Check> */
	private array $checks = [];

	public function __construct(private readonly Practice $plugin){
		$this->plugin->getServer()->getPluginManager()->registerEvents(new AnticheatListener($this), $this->plugin);
		foreach([
			new ReachA(),
			new ReachB(),
			new ReachC(),
			new ReachD(),
			new FlyA(),
			new FlyB()] as $check){
			$this->registerCheck($check);
		}
	}

	private function registerCheck(Check $check): void{
		$name = $check->getName() . $check->getSubType();
		if(isset($this->checks[$name])){
			throw new \RuntimeException('Check ' . $name . ' already registered.');
		}

		$this->checks[$name] = $check;
	}

	public function getAllChecks(): array{
		return $this->checks;
	}
}
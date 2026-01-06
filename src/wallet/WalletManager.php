<?php

declare(strict_types=1);

namespace collapse\wallet;

use collapse\Practice;
use collapse\wallet\command\CurrencyCommand;

final readonly class WalletManager{

	public function __construct(private Practice $plugin){
		$this->plugin->getServer()->getCommandMap()->register('collapse', new CurrencyCommand($this));
	}

	public function getPlugin() : Practice{
		return $this->plugin;
	}
}

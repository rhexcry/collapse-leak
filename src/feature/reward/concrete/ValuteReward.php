<?php

declare(strict_types=1);

namespace collapse\feature\reward\concrete;

use collapse\feature\reward\Reward;
use collapse\player\CollapsePlayer;
use collapse\wallet\currency\Currencies;
use collapse\wallet\Wallet;

final class ValuteReward implements Reward{

	public function __construct(
		private int $amount
	){
	}

	public function apply(CollapsePlayer $player) : void{
		Wallet::add(Currencies::DUST(), $player->getProfile(), $this->amount);
	}

	public function getAmount() : int{
		return $this->amount;
	}

	public function setAmount(int $amount) : void{
		$this->amount = $amount;
	}
}

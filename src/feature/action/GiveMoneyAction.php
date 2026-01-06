<?php

declare(strict_types=1);

namespace collapse\feature\action;

use collapse\player\CollapsePlayer;

final readonly class GiveMoneyAction implements IAction{

	public function __construct(private string $amount){}

	public function execute(CollapsePlayer $player) : void{
		//TODO
	}
}

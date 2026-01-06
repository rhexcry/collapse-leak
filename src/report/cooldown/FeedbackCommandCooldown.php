<?php

declare(strict_types=1);

namespace collapse\report\cooldown;

use collapse\cooldown\TickingCooldown;
use collapse\cooldown\types\CooldownType;

final class FeedbackCommandCooldown extends TickingCooldown{

	private const int DEFAULT_TICKS = 5 * 60 * 20;

	private int $ticks = self::DEFAULT_TICKS;

	public function getType() : CooldownType{
		return CooldownType::Command;
	}

	protected function getTicks() : int{
		return 2;
	}

	protected function onStartTicking() : void{}

	protected function onCompletedTicking() : void{}

	protected function onTick() : void{
		if($this->ticks <= 0){
			$this->forceComplete();
			return;
		}

		--$this->ticks;
	}
}

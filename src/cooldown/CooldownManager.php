<?php

declare(strict_types=1);

namespace collapse\cooldown;

use collapse\cooldown\types\CooldownType;
use collapse\player\CollapsePlayer;

final class CooldownManager{

	/** @var Cooldown[][] */
	private array $cooldowns = [];

	public function addCooldown(CollapsePlayer $player, Cooldown $cooldown) : void{
		$this->cooldowns[$player->getName()][$cooldown->getType()->name] = $cooldown;
		$cooldown->onStart();
	}

	public function getCooldowns(CollapsePlayer $player) : array{
		return $this->cooldowns[$player->getName()] ?? [];
	}

	public function cancel(CollapsePlayer $player, CooldownType $type) : void{
		if(isset($this->cooldowns[$player->getName()][$type->name])){
			$this->cooldowns[$player->getName()][$type->name]->onCompletion();
			unset($this->cooldowns[$player->getName()][$type->name]);
		}
	}

	public function cancelAll(CollapsePlayer $player) : void{
		if(isset($this->cooldowns[$player->getName()])){
			foreach($this->cooldowns[$player->getName()] as $cooldown){
				$cooldown->onCompletion();
			}
			unset($this->cooldowns[$player->getName()]);
		}
	}

	public function hasCooldown(CollapsePlayer $player, CooldownType $type) : bool{
		if(isset($this->cooldowns[$player->getName()][$type->name])){
			$cooldown = $this->cooldowns[$player->getName()][$type->name];
			if($cooldown->isActive()){
				return true;
			}
			$this->cancel($player, $type);
		}
		return false;
	}
}

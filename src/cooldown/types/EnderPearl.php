<?php

declare(strict_types=1);

namespace collapse\cooldown\types;

use collapse\cooldown\TickingCooldown;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\world\sound\MinecraftSound;
use collapse\world\sound\MinecraftSoundNames;

final class EnderPearl extends TickingCooldown implements GameCooldown{

	private int $ticks;

	public function __construct(CollapsePlayer $player, int $duration = 15){
		parent::__construct($player, $duration);

		$this->ticks = $duration * 10;
	}

	public function getType() : CooldownType{
		return CooldownType::EnderPearl;
	}

	protected function getTicks() : int{
		return 2;
	}

	protected function onStartTicking() : void{
		/*$this->player->sendTranslatedMessage(CollapseTranslationFactory::cooldown_ender_pearl_started());
		$this->player->sendTranslatedPopup(CollapseTranslationFactory::cooldown_ender_pearl_started());*/
	}

	protected function onCompletedTicking() : void{
		if(!$this->player->isConnected()){
			return;
		}
		$this->player->getXpManager()->setXpLevel(0);
		$this->player->getXpManager()->setXpProgress(0);
	}

	protected function onTick() : void{
		if($this->ticks <= 0){
			$this->forceComplete();
			//$this->player->sendTranslatedMessage(CollapseTranslationFactory::cooldown_ender_pearl_ended());
			$this->player->sendTranslatedPopup(CollapseTranslationFactory::cooldown_ender_pearl_ended());
			$this->player->getWorld()->addSound($this->player->getLocation(), new MinecraftSound(MinecraftSoundNames::NOTE_BASS), [$this->player]);
		}else{
			$this->player->getXpManager()->setXpLevel((int) (($this->ticks / 10) + 0.9));
			$this->player->getXpManager()->setXpProgress((float) $this->ticks / ($this->duration * 10));
			--$this->ticks;
		}
	}
}

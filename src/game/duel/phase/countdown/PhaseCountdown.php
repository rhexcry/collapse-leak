<?php

declare(strict_types=1);

namespace collapse\game\duel\phase\countdown;

use collapse\game\duel\phase\Phase;
use collapse\game\event\BlockBreakGameEvent;
use collapse\game\event\BlockPlaceGameEvent;
use collapse\player\CollapsePlayer;
use pocketmine\world\sound\ClickSound;

class PhaseCountdown extends Phase{

	private int $countdown = 5;

	public function setScoreboard(CollapsePlayer $player) : void{
		$player->setScoreboard(new PhaseCountdownScoreboard($player, $this->duel, $this));
	}

	public function onUpdate() : void{
		if($this->countdown <= 0){
			$this->duel->getPhaseManager()->setPhase($this->duel->getPhaseManager()->getBasePhaseRunning());
			foreach($this->duel->getPlayerManager()->getPlayers() as $player){
				$player->sendTitle('');
			}
		}else{
			$symbol = match ($this->countdown){
				5 => '',
				4 => '',
				3 => '',
				2 => '',
				1 => ''
			};
			foreach($this->duel->getPlayerManager()->getPlayers() as $player){
				$player->sendTitle($symbol);
				//$player->sendMessage(TextFormat::AQUA . $this->countdown . '...', false);
				$player->getWorld()->addSound($player->getLocation(), new ClickSound(), [$player]);
			}
		}
		--$this->countdown;
	}

	public function handleBlockBreak(BlockBreakGameEvent $event) : void{
		$event->cancel();
	}

	public function handleBlockPlace(BlockPlaceGameEvent $event) : void{
		$event->cancel();
	}
}

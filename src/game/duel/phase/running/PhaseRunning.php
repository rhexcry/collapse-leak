<?php

declare(strict_types=1);

namespace collapse\game\duel\phase\running;

use collapse\cosmetics\effects\death\DeadBodyDeathEffect;
use collapse\game\duel\phase\Phase;
use collapse\game\event\PlayerAttackPlayerGameEvent;
use collapse\game\event\PlayerDamageGameEvent;
use collapse\game\event\PlayerDeathGameEvent;
use collapse\game\event\PlayerKillPlayerGameEvent;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\world\sound\MinecraftSound;
use collapse\world\sound\MinecraftSoundNames;

class PhaseRunning extends Phase{

	public function setScoreboard(CollapsePlayer $player) : void{
		$player->setScoreboard(new PhaseRunningScoreboard($player, $this->duel, $this));
	}

	public function onSet() : void{
		foreach($this->duel->getPlayerManager()->getPlayers() as $player){
			$player->sendTranslatedMessage(CollapseTranslationFactory::duels_match_started());
			$player->getWorld()->addSound($player->getLocation(), new MinecraftSound(MinecraftSoundNames::FIREWORK_BLAST), [$player]);
			if($player->hasNoClientPredictions()){
				$player->setNoClientPredictions(false);
			}
		}
	}

	public function onUpdate() : void{}

	public function handlePlayerAttackPlayer(PlayerAttackPlayerGameEvent $event) : void{}

	public function handlePlayerDamage(PlayerDamageGameEvent $event) : void{}

	public function handlePlayerDeath(PlayerDeathGameEvent $event) : void{
		$this->duel->getPlayerManager()->onPlayerDied($event->getPlayer());
	}

	public function handlePlayerKillPlayer(PlayerKillPlayerGameEvent $event) : void{
		$player = $event->getPlayer();
		$killer = $event->getKiller();
		new DeadBodyDeathEffect($player, $killer);

		$player->getProfile()->getDeathEffect()?->create($player, $killer);

		$this->duel->getPlayerManager()->onPlayerDied($player);
	}
}

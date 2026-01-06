<?php

declare(strict_types=1);

namespace collapse\game\duel\modes\skywars\phase;

use collapse\game\duel\Duel;
use collapse\game\duel\modes\basic\KillMessagesHandler;
use collapse\game\duel\modes\skywars\SkyWars;
use collapse\game\duel\phase\running\PhaseRunning;
use collapse\game\event\PlayerDamageGameEvent;
use collapse\game\event\PlayerDeathGameEvent;
use collapse\game\event\PlayerKillPlayerGameEvent;
use collapse\player\CollapsePlayer;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\world\sound\XpCollectSound;

final class SkyWarsPhaseRunning extends PhaseRunning{
	use KillMessagesHandler;

	/** @var SkyWars */
	protected readonly Duel $duel;

	public function setScoreboard(CollapsePlayer $player) : void{
		$player->setScoreboard(new SkyWarsPhaseRunningScoreboard($player, $this->duel, $this));
	}

	public function handlePlayerDamage(PlayerDamageGameEvent $event) : void{
		if($event->getSubEvent()->getCause() === EntityDamageEvent::CAUSE_VOID){
			(new PlayerDeathGameEvent($this->duel, $event->getPlayer(), $event->getSubEvent()->getCause()))->call();
			return;
		}

		parent::handlePlayerDamage($event);
	}

	public function handlePlayerDeath(PlayerDeathGameEvent $event) : void{
		$player = $event->getPlayer();
		$this->broadcastDeathMessage($player, $event->getCause());
		$this->duel->getPlayerManager()->onPlayerDied($player);
		$this->duel->getSkyWarsTeamManager()->onForceScoreboardUpdate();
	}

	public function handlePlayerKillPlayer(PlayerKillPlayerGameEvent $event) : void{
		$player = $event->getPlayer();
		$killer = $event->getKiller();
		$this->broadcastDeathMessage($player, $event->getCause(), $killer);
		if($this->duel->getPlayerManager()->hasPlayer($player)){
			$killer->getWorld()->addSound($killer->getLocation(), new XpCollectSound(), [$killer]);
		}

		$this->duel->getPlayerManager()->onPlayerDied($player);
		$this->duel->getSkyWarsTeamManager()->onForceScoreboardUpdate();
	}
}

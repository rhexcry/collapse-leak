<?php

declare(strict_types=1);

namespace collapse\game\duel\modes\basic;

use collapse\game\duel\Duel;
use collapse\game\duel\modes\basic\event\BedDestroyEvent;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\world\sound\MinecraftSound;
use collapse\world\sound\MinecraftSoundNames;
use pocketmine\world\sound\XpCollectSound;
use function array_merge;

trait BedsEventListener{

	/**
	 * @priority LOWEST
	 */
	public function handleBedDestroy(BedDestroyEvent $event) : void{
		$team = $event->getTeam();
		$player = $event->getPlayer();
		if($team === $player->getTeam()){
			$event->cancel();
			$player->sendTranslatedMessage(CollapseTranslationFactory::duels_base_beds_destroy_self());
			return;
		}
		$duel = $event->getTeam()->getGame();
		if(!$duel instanceof Duel){
			$event->cancel();
			return;
		}
		$player->getWorld()->addSound($player->getLocation(), new XpCollectSound(), [$player]);
		foreach(array_merge($duel->getPlayerManager()->getPlayers(), $duel->getSpectatorManager()->getSpectators()) as $otherPlayer){
			if(!$otherPlayer instanceof CollapsePlayer || !$player->isConnected()){
				continue;
			}
			$otherPlayer->sendTranslatedMessage(CollapseTranslationFactory::duels_base_bed_destroyed($team->getColor(), $team->getName(), $player->getTeam()->getColor() . $player->getName()), false);
		}
		foreach($team->getPlayers() as $teamPlayer){
			if(!$teamPlayer->isConnected()){
				continue;
			}
			$teamPlayer->sendTranslatedTitle(
				CollapseTranslationFactory::duels_base_bed_destroyed_title(),
				CollapseTranslationFactory::duels_base_bed_destroyed_subtitle()
			);
			$teamPlayer->getWorld()->addSound($player->getLocation(), new MinecraftSound(MinecraftSoundNames::MOB_WITHER_DEATH), [$teamPlayer]);
		}
	}
}

<?php

declare(strict_types=1);

namespace collapse\player\settings;

use collapse\player\CollapsePlayer;
use collapse\player\scoreboard\HiddenScoreboard;
use collapse\player\scoreboard\ScoreboardSetEvent;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\types\PlayerAuthInputFlags;

final readonly class SettingsListener implements Listener{

	/**
	 * @priority LOWEST
	 */
	public function handleDataPacketReceive(DataPacketReceiveEvent $event) : void{
		$packet = $event->getPacket();
		if($packet instanceof PlayerAuthInputPacket){
			$player = $event->getOrigin()->getPlayer();
			if(!($player instanceof CollapsePlayer && $player->getProfile() !== null)){
				return;
			}
			if($player->isSprinting() || $player->isSneaking() || $packet->getInputFlags()->get(PlayerAuthInputFlags::SNEAKING)){
				return;
			}
			if($player->getProfile()->getSetting(Setting::AutoSprint) && $packet->getInputFlags()->get(PlayerAuthInputFlags::UP)){
				$player->setSprinting();
			}
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handleScoreboardSet(ScoreboardSetEvent $event) : void{
		$player = $event->getPlayer();
		$scoreboard = $event->getScoreboard();
		if($player->getProfile()?->getSetting(Setting::HideScoreboard) && $scoreboard instanceof HiddenScoreboard){
			$event->cancel();
		}
	}
}

<?php

declare(strict_types=1);

namespace collapse\system\moderatorpoints;

use collapse\player\CollapsePlayer;
use collapse\punishments\event\ProfilePreKickEvent;
use collapse\punishments\event\ProfilePrePunishEvent;
use collapse\punishments\PunishmentType;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;

final readonly class ModeratorPointsListener implements Listener{

	public function __construct(
		private ModeratorPointsManager $manager
	){}

	public function handlePlayerMove(PlayerMoveEvent $event) : void{
		$player = $event->getPlayer();
		if($player instanceof CollapsePlayer && $player->getProfile()?->getRank()->isModeratorRank()){
			$this->manager->setLastMovement($player);
		}
	}

	public function onPrePunish(ProfilePrePunishEvent $event) : void{
		if($event->isCancelled()){
			return;
		}

		$punishment = $event->getPunishment();
		$sender = $event->getSender();
		if(!$sender instanceof CollapsePlayer){
			return;
		}

		$type = match($punishment->getType()){
			PunishmentType::Ban => 'ban',
			PunishmentType::Mute => 'mute',
			default => null
		};
		if($type !== null){
			$this->manager->addMpForPunishment($sender, $type);
		}
	}

	public function onPreKick(ProfilePreKickEvent $event) : void{
		if($event->isCancelled()){
			return;
		}
		$sender = $event->getSender();
		if(!$sender instanceof CollapsePlayer){
			return;
		}
		$this->manager->addMpForPunishment($sender, 'kick');
	}
}

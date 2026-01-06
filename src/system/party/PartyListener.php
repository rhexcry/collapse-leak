<?php

declare(strict_types=1);

namespace collapse\system\party;

use collapse\player\CollapsePlayer;
use collapse\Practice;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;

final readonly class PartyListener implements Listener{

	public function handlePlayerQuit(PlayerQuitEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();

		$partyManager = Practice::getInstance()->getPartyManager();
		if ($partyManager->isInParty($player)) {
			$party = $partyManager->getPlayerParty($player);
			if ($party->getLeader() === $player) {
				$partyManager->disbandParty($party);
			} else {
				$party->removeMember($player);
			}
		}
	}
}
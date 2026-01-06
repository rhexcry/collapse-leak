<?php

declare(strict_types=1);

namespace collapse\game\duel\queue;

use collapse\game\duel\Duel;
use collapse\game\duel\queue\entry\SoloQueueEntry;
use collapse\game\duel\queue\event\DuelMatchFoundEvent;
use collapse\game\duel\types\DuelType;

final class UnrankedQueue extends Queue{

	/** @var \SplObjectStorage<SoloQueueEntry> */
	protected \SplObjectStorage $entries;

	public function onUpdate() : void{
		$entries = clone $this->entries;
		while($entries->count() >= 2){
			$entries->detach($first = $entries->current());
			$entries->detach($second = $entries->current());
			$this->onMembersFound([$first->getMembers(), $second->getMembers()]);
		}
	}

	protected function onMembersFound(array $members) : void{
		$event = new DuelMatchFoundEvent($members, $this);
		$event->call();

		if($event->isCancelled()){
			return;
		}

		$this->queueManager->getDuelManager()->add(
			$this->queueManager->getDuelManager()->getMapPool()->getRandom($this->mode),
			DuelType::Unranked
		)->onCreate(function(Duel $duel) use ($members) : void{
			foreach($members as $member){
				if(!$member->isConnected() || $member->isInGame()){
					return;
				}
			}

			foreach($members as $member){
				$this->queueManager->getDuelManager()->getPlugin()->getLobbyManager()->showToPlayers($member);
			}
			foreach($members as $member){
				$this->queueManager->getDuelManager()->getPlugin()->getLobbyManager()->removeFromLobby($member);
				$this->queueManager->removeFromSoloQueue($member);
				$duel->getPlayerManager()->addPlayer($member);
			}
		});
	}
}
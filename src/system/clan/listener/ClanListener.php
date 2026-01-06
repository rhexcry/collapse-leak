<?php

declare(strict_types=1);

namespace collapse\system\clan\listener;

use collapse\player\profile\event\ProfileLoadedEvent;
use collapse\system\clan\ClanManager;
use collapse\system\clan\event\ClanCreatedEvent;
use pocketmine\event\Listener;

final readonly class ClanListener implements Listener{

	public function __construct(private ClanManager $clanManager){}

	public function handleClaCreated(ClanCreatedEvent $event) : void{
		$clan = $event->getClan();

	}

	public function handleProfileLoaded(ProfileLoadedEvent $event) : void{
		// HACK: я в рот ебал эту хуйню, какого-то хуя без этого клан айди === null, если это не сделать
		// например в командах будет писать что клана нет (clan info, clan delete)
		$event->getProfile()->setClanId($event->getProfile()->getClanId());
	}
}

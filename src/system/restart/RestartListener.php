<?php

declare(strict_types=1);

namespace collapse\system\restart;

use collapse\game\duel\queue\event\DuelMatchFoundEvent;
use pocketmine\event\Listener;

final class RestartListener implements Listener{

	public function __construct(private readonly RestartManager $restartManager){}

	public function handleMatchFound(DuelMatchFoundEvent $event): void{
	}
}
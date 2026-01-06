<?php

declare(strict_types=1);

namespace collapse\game\duel\phase;

use collapse\game\event\BlockBreakGameEvent;
use collapse\game\event\BlockPlaceGameEvent;
use collapse\game\event\PlayerAttackPlayerGameEvent;
use collapse\game\event\PlayerDamageGameEvent;
use collapse\game\event\PlayerDeathGameEvent;
use collapse\game\event\PlayerKillPlayerGameEvent;

interface PhaseEventHandlerInterface{

	public function handlePlayerAttackPlayer(PlayerAttackPlayerGameEvent $event) : void;

	public function handlePlayerDamage(PlayerDamageGameEvent $event) : void;

	public function handlePlayerDeath(PlayerDeathGameEvent $event) : void;

	public function handlePlayerKillPlayer(PlayerKillPlayerGameEvent $event) : void;

	public function handleBlockBreak(BlockBreakGameEvent $event) : void;

	public function handleBlockPlace(BlockPlaceGameEvent $event) : void;
}

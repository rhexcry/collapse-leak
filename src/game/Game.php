<?php

declare(strict_types=1);

namespace collapse\game;

use collapse\game\statistics\GameStatisticsManager;
use collapse\player\CollapsePlayer;
use collapse\Practice;

interface Game{

	public function getPlugin() : Practice;

	public function getStatistics(?CollapsePlayer $player = null) : ?GameStatisticsManager;

	public function onPlayerJoin(CollapsePlayer $player, \Closure $callback) : void;

	public function onPlayerLeave(CollapsePlayer $player) : void;

	public function isBlocksActions() : bool;

	public function isDamageDisabled() : bool;

	public function isFallDamageDisabled() : bool;

	public function isEnderPearlCooldown() : bool;

	public function isHidePlayersInCombat() : bool;

	public function isCombat() : bool;

	public function isStatisticsEnabled() : bool;

	public function hasRandomSpawn() : bool;
}

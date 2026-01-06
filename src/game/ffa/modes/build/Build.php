<?php

declare(strict_types=1);

namespace collapse\game\ffa\modes\build;

use collapse\game\ffa\FreeForAllArena;
use collapse\utils\EventUtils;
use pocketmine\math\AxisAlignedBB;
use function max;
use function min;

final class Build extends FreeForAllArena{

	private BuildBlockManager $blockManager;

	private AxisAlignedBB $spawnBounds;

	protected function setUp() : void{
		EventUtils::registerListenerOnce(new BuildListener());
		$this->blockManager = new BuildBlockManager($this);

		$spawnBounds = $this->config->getExtraData()[BuildConfigKeys::SPAWN_BOUNDS];
		$this->spawnBounds = new AxisAlignedBB(
			min($spawnBounds[BuildConfigKeys::SPAWN_BOUNDS_MIN_X], $spawnBounds[BuildConfigKeys::SPAWN_BOUNDS_MAX_X]),
			min($spawnBounds[BuildConfigKeys::SPAWN_BOUNDS_MIN_Y], $spawnBounds[BuildConfigKeys::SPAWN_BOUNDS_MAX_Y]),
			min($spawnBounds[BuildConfigKeys::SPAWN_BOUNDS_MIN_Z], $spawnBounds[BuildConfigKeys::SPAWN_BOUNDS_MAX_Z]),
			max($spawnBounds[BuildConfigKeys::SPAWN_BOUNDS_MIN_X], $spawnBounds[BuildConfigKeys::SPAWN_BOUNDS_MAX_X]),
			max($spawnBounds[BuildConfigKeys::SPAWN_BOUNDS_MIN_Y], $spawnBounds[BuildConfigKeys::SPAWN_BOUNDS_MAX_Y]),
			max($spawnBounds[BuildConfigKeys::SPAWN_BOUNDS_MIN_Z], $spawnBounds[BuildConfigKeys::SPAWN_BOUNDS_MAX_Z])
		);
	}

	public function getBlockManager() : BuildBlockManager{
		return $this->blockManager;
	}

	public function getSpawnBounds() : AxisAlignedBB{
		return $this->spawnBounds;
	}

	public function isBlocksActions() : bool{
		return true;
	}

	public function isAntiInterrupt() : bool{
		return false;
	}

	public function isStatisticsEnabled() : bool{
		return false;
	}

	public function isCombat() : bool{
		return true;
	}

	public function isHidePlayersInCombat() : bool{
		return false;
	}
}

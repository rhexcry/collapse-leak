<?php

declare(strict_types=1);

namespace collapse\game\ffa\modes\crystal;

use collapse\game\ffa\FreeForAllArena;
use collapse\utils\EventUtils;
use pocketmine\math\AxisAlignedBB;
use function max;
use function min;

final class Crystal extends FreeForAllArena{

	private CrystalBlockManager $blockManager;

	private AxisAlignedBB $spawnBounds;

	protected function setUp() : void{
		EventUtils::registerListenerOnce(new CrystalListener());
		$this->blockManager = new CrystalBlockManager($this);

		$spawnBounds = $this->config->getExtraData()[CrystalConfigKeys::SPAWN_BOUNDS];
		$this->spawnBounds = new AxisAlignedBB(
			min($spawnBounds[CrystalConfigKeys::SPAWN_BOUNDS_MIN_X], $spawnBounds[CrystalConfigKeys::SPAWN_BOUNDS_MAX_X]),
			min($spawnBounds[CrystalConfigKeys::SPAWN_BOUNDS_MIN_Y], $spawnBounds[CrystalConfigKeys::SPAWN_BOUNDS_MAX_Y]),
			min($spawnBounds[CrystalConfigKeys::SPAWN_BOUNDS_MIN_Z], $spawnBounds[CrystalConfigKeys::SPAWN_BOUNDS_MAX_Z]),
			max($spawnBounds[CrystalConfigKeys::SPAWN_BOUNDS_MIN_X], $spawnBounds[CrystalConfigKeys::SPAWN_BOUNDS_MAX_X]),
			max($spawnBounds[CrystalConfigKeys::SPAWN_BOUNDS_MIN_Y], $spawnBounds[CrystalConfigKeys::SPAWN_BOUNDS_MAX_Y]),
			max($spawnBounds[CrystalConfigKeys::SPAWN_BOUNDS_MIN_Z], $spawnBounds[CrystalConfigKeys::SPAWN_BOUNDS_MAX_Z])
		);
	}

	public function isBlocksActions() : bool{
		return true;
	}

	public function isAntiInterrupt() : bool{
		return false;
	}

	public function isEnderPearlCooldown() : bool{
		return true;
	}

	public function isHidePlayersInCombat() : bool{
		return false;
	}

	public function isCombat() : bool{
		return true;
	}

	public function isStatisticsEnabled() : bool{
		return false;
	}

	public function getBlockManager() : CrystalBlockManager{
		return $this->blockManager;
	}

	public function getSpawnBounds() : AxisAlignedBB{
		return $this->spawnBounds;
	}
}
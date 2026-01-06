<?php

declare(strict_types=1);

namespace collapse\game\ffa\modes\midfight;

use collapse\game\ffa\FreeForAllArena;
use collapse\utils\EventUtils;
use pocketmine\entity\Location;

final class MidFight extends FreeForAllArena{

	private array $randomSpawnsMap = [];

	protected function setUp() : void{
		EventUtils::registerListenerOnce(new MidFightListener());

		$randomSpawns = $this->config->getExtraData()[MidFightConfigKeys::RANDOM_SPAWNS] ?? [];
		if(empty($randomSpawns)){
			$this->randomSpawnsMap[] = $this->config->getSpawnLocation();
			return;
		}

		$world = $this->config->getSpawnLocation()->getWorld();
		foreach($randomSpawns as $data){
			$this->randomSpawnsMap[] = new Location(
				$data[MidFightConfigKeys::RANDOM_SPAWN_X],
				$data[MidFightConfigKeys::RANDOM_SPAWN_Y],
				$data[MidFightConfigKeys::RANDOM_SPAWN_Z],
				$world, 0, 0);
		}
	}

	public function isBlocksActions() : bool{
		return false;
	}

	public function isAntiInterrupt() : bool{
		return false;
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

	public function getRandomSpawn() : Location{
		return $this->randomSpawnsMap[array_rand($this->randomSpawnsMap)];
	}

	public function hasRandomSpawn() : bool{
		return true;
	}
}
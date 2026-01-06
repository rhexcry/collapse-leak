<?php

declare(strict_types=1);

namespace collapse\cosmetics\effects\death;

use pocketmine\world\particle\HugeExplodeSeedParticle;
use function array_merge;

final readonly class ExplodeDeathEffect extends DeathEffect{

	protected function spawn() : void{
		$this->player->getWorld()->addParticle($this->player->getLocation(), new HugeExplodeSeedParticle(), array_merge([$this->player], $this->player->getViewers()));
	}
}

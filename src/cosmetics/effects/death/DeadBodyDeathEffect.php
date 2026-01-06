<?php

declare(strict_types=1);

namespace collapse\cosmetics\effects\death;

use collapse\world\sound\MinecraftSound;
use collapse\world\sound\MinecraftSoundNames;

final readonly class DeadBodyDeathEffect extends DeathEffect{

	protected function spawn() : void{
		$entity = new DeadBodyEntity($location = $this->player->getLocation(), $this->player->getSkin());
		$entity->spawnToAll();
		$killerLocation = $this->killer->getLocation();
		$entity->broadcastSound(new MinecraftSound(MinecraftSoundNames::GAME_PLAYER_HURT));
		$entity->kill();
		$entity->knockBack($location->x - $killerLocation->x, $location->z - $killerLocation->z);
	}
}

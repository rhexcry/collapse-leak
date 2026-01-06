<?php

declare(strict_types=1);

namespace collapse\lobby;

use collapse\npc\location\DefaultNPCLocationDefinitions;
use collapse\player\CollapsePlayer;
use collapse\world\area\Area;
use collapse\world\sound\MinecraftSound;
use collapse\world\sound\MinecraftSoundNames;
use pocketmine\world\particle\FlameParticle;

final class LobbyBoostPad extends Area{

	private const float POWER = 2.5;

	public function onEnter(CollapsePlayer $player) : void{
		$nearestNpcPos = DefaultNPCLocationDefinitions::FFA->getLocation()->asPosition();

		$direction = $nearestNpcPos->subtractVector($player->getPosition())->normalize();
		$direction = $direction->multiply(self::POWER);
		$direction->y = 0.8;

		$player->setMotion($direction);

		$player->getWorld()->addParticle($player->getPosition(), new FlameParticle());
		$player->getWorld()->addSound($player->getPosition(), new MinecraftSound(MinecraftSoundNames::MOB_SHULKER_SHOOT), [$player]);
	}

	public function onLeave(CollapsePlayer $player) : void{

	}
}

<?php

declare(strict_types=1);

namespace collapse\utils;

use collapse\player\CollapsePlayer;
use pocketmine\block\BlockTypeIds;
use pocketmine\entity\Location;
use pocketmine\world\Position;

final class TeleportUtils{

	public static function safeTeleport(CollapsePlayer $player, Position $location) : bool{
		$world = $location->getWorld();
		if(!$world->isLoaded()){
			return false;
		}

		$safeLocation = self::getSafeSpawn($location);
		if($safeLocation === null){
			return false;
		}

		return $player->teleport($location, safe: false);
	}

	public static function getSafeSpawn(Position $location) : ?Position{
		$world = $location->getWorld();
		if(!$world->isLoaded()){
			return null;
		}

		$chunkX = $location->getFloorX() >> 4;
		$chunkZ = $location->getFloorZ() >> 4;

		if(!$world->isChunkLoaded($chunkX, $chunkZ)){
			$world->loadChunk($chunkX, $chunkZ);
		}
		$world->loadChunk($chunkX + 1, $chunkZ);
		$world->loadChunk($chunkX - 1, $chunkZ);
		$world->loadChunk($chunkX, $chunkZ + 1);
		$world->loadChunk($chunkX, $chunkZ - 1);

		$x = $location->getFloorX();
		$z = $location->getFloorZ();
		$y = (int) min($world->getMaxY() - 2, $location->getFloorY());
		$minY = $world->getMinY();
		$maxY = $world->getMaxY();

		$wasAir = $world->getBlockAt($x, $y - 1, $z)->getTypeId() === BlockTypeIds::AIR;
		for(; $y > $minY; --$y){
			$block = $world->getBlockAt($x, $y, $z);
			if($block->isFullCube()){
				if($wasAir){
					$y++;
				}
				break;
			}else{
				$wasAir = true;
			}
		}

		for(; $y >= $minY && $y < $maxY; ++$y){
			$blockAbove = $world->getBlockAt($x, $y + 1, $z);
			$blockCurrent = $world->getBlockAt($x, $y, $z);

			if(!$blockAbove->isFullCube()){
				if(!$blockCurrent->isFullCube()){
					if($location instanceof Location){
						return new Location(
							$location->x,
							$y === $location->getFloorY() ? $location->y : (float) $y,
							$location->z,
							$world,
							$location->yaw,
							$location->pitch
						);
					}else{
						return new Position(
							$location->x,
							$y === $location->getFloorY() ? $location->y : (float) $y,
							$location->z,
							$world
						);
					}
				}
			}else{
				++$y;
			}
		}

		return $location;
	}

	public static function isLocationSafe(Position $location) : bool{
		$world = $location->getWorld();
		if(!$world->isLoaded()){
			return false;
		}

		$chunkX = $location->getFloorX() >> 4;
		$chunkZ = $location->getFloorZ() >> 4;

		return $world->isChunkLoaded($chunkX, $chunkZ) &&
			$world->isChunkLoaded($chunkX + 1, $chunkZ) &&
			$world->isChunkLoaded($chunkX - 1, $chunkZ) &&
			$world->isChunkLoaded($chunkX, $chunkZ + 1) &&
			$world->isChunkLoaded($chunkX, $chunkZ - 1);
	}
}
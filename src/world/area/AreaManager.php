<?php

declare(strict_types=1);

namespace collapse\world\area;

use collapse\player\CollapsePlayer;
use collapse\Practice;
use pocketmine\world\format\Chunk;
use pocketmine\world\World;

final class AreaManager{

	/** @var Area[] */
	private array $areas = [];

	/** @var Area[][][] */
	private array $areasByChunk = [];

	public function __construct(
		private readonly Practice $plugin
	){
		$this->plugin->getServer()->getPluginManager()->registerEvents(new AreaListener($this), $this->plugin);
	}

	public function getPlugin() : Practice{
		return $this->plugin;
	}

	public function add(Area $area) : void{
		$worldName = $area->getWorld()->getFolderName();
		$this->areas[$worldName][$area->getId()] = $area;
		$boundingBox = $area->getBoundingBox();
		for($x = $boundingBox->minX >> Chunk::COORD_BIT_SIZE; $x <= $boundingBox->maxX >> Chunk::COORD_BIT_SIZE; $x++){
			for($z = $boundingBox->minZ >> Chunk::COORD_BIT_SIZE; $z <= $boundingBox->maxZ >> Chunk::COORD_BIT_SIZE; $z++){
				$this->areasByChunk[$worldName][World::chunkHash($x, $z)][] = $area;
				/** @var CollapsePlayer $player */
				foreach($area->getWorld()->getChunkPlayers($x, $z) as $player){
					$this->onChunkIntersection($player);
				}
			}
		}
	}

	public function getAreasByChunk(World $world, int $x, int $z) : array{
		return $this->areasByChunk[$world->getFolderName()][World::chunkHash($x, $z)] ?? [];
	}

	public function onChunkIntersection(CollapsePlayer $player) : void{
		$boundingBox = $player->getBoundingBox();
		foreach($player->getCollidedAreas() as $area){
			if($area->isCollidesWith($boundingBox)){
				continue;
			}
			$player->removeCollidedArea($area);
		}

		foreach($this->getAreasByChunk(
			$player->getWorld(),
			$player->getPosition()->getFloorX() >> Chunk::COORD_BIT_SIZE,
			$player->getPosition()->getFloorZ() >> Chunk::COORD_BIT_SIZE
		) as $area){
			if(!$area->isCollidesWith($boundingBox)){
				continue;
			}
			$player->addCollidedArea($area);
		}
	}
}

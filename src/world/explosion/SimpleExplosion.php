<?php

declare(strict_types=1);

namespace collapse\world\explosion;

use InvalidArgumentException;
use pocketmine\block\Block;
use pocketmine\block\RuntimeBlockStateRegistry;
use pocketmine\entity\Entity;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\world\format\SubChunk;
use pocketmine\world\Position;
use pocketmine\world\utils\SubChunkExplorer;
use pocketmine\world\utils\SubChunkExplorerStatus;
use pocketmine\world\World;
use function mt_rand;
use function sqrt;

abstract class SimpleExplosion{

	private int $rays = 16;
	public World $world;

	/** @var Block[] */
	public array $affectedBlocks = [];
	public float $stepLen = 0.3;

	private SubChunkExplorer $subChunkExplorer;

	public function __construct(
		public Position $source,
		public float $radius,
		protected readonly Entity|Block|null $what = null
	){
		if(!$this->source->isValid()){
			throw new InvalidArgumentException('Position does not have a valid world');
		}
		$this->world = $this->source->getWorld();

		if($radius <= 0){
			throw new InvalidArgumentException('Explosion radius must be greater than 0, got ' . $radius);
		}
		$this->subChunkExplorer = new SubChunkExplorer($this->world);
	}

	public function explodeA() : bool{
		if($this->radius < 0.1){
			return false;
		}

		$blockFactory = RuntimeBlockStateRegistry::getInstance();
		$mRays = $this->rays - 1;
		for($i = 0; $i < $this->rays; ++$i){
			for($j = 0; $j < $this->rays; ++$j){
				for($k = 0; $k < $this->rays; ++$k){
					if($i === 0 || $i === $mRays || $j === 0 || $j === $mRays || $k === 0 || $k === $mRays){
						[$shiftX, $shiftY, $shiftZ] = [$i / $mRays * 2 - 1, $j / $mRays * 2 - 1, $k / $mRays * 2 - 1];
						$len = sqrt($shiftX ** 2 + $shiftY ** 2 + $shiftZ ** 2);
						[$shiftX, $shiftY, $shiftZ] = [($shiftX / $len) * $this->stepLen, ($shiftY / $len) * $this->stepLen, ($shiftZ / $len) * $this->stepLen];
						$pointerX = $this->source->x;
						$pointerY = $this->source->y;
						$pointerZ = $this->source->z;

						for($blastForce = $this->radius * (mt_rand(700, 1300) / 1000); $blastForce > 0; $blastForce -= $this->stepLen * 0.75){
							$x = (int) $pointerX;
							$y = (int) $pointerY;
							$z = (int) $pointerZ;
							$vBlockX = $pointerX >= $x ? $x : $x - 1;
							$vBlockY = $pointerY >= $y ? $y : $y - 1;
							$vBlockZ = $pointerZ >= $z ? $z : $z - 1;

							$pointerX += $shiftX;
							$pointerY += $shiftY;
							$pointerZ += $shiftZ;

							if($this->subChunkExplorer->moveTo($vBlockX, $vBlockY, $vBlockZ) === SubChunkExplorerStatus::INVALID){
								continue;
							}
							$subChunk = $this->subChunkExplorer->currentSubChunk;
							if($subChunk === null){
								throw new AssumptionFailedError('SubChunkExplorer subchunk should not be null here');
							}

							$state = $subChunk->getBlockStateId($vBlockX & SubChunk::COORD_MASK, $vBlockY & SubChunk::COORD_MASK, $vBlockZ & SubChunk::COORD_MASK);

							$blastResistance = $blockFactory->blastResistance[$state] ?? 0;
							if($blastResistance >= 0){
								$blastForce -= ($blastResistance / 5 + 0.3) * $this->stepLen;
								if($blastForce > 0){
									if(!isset($this->affectedBlocks[World::blockHash($vBlockX, $vBlockY, $vBlockZ)])){
										$_block = $this->world->getBlockAt($vBlockX, $vBlockY, $vBlockZ, true, false);
										foreach($_block->getAffectedBlocks() as $_affectedBlock){
											$_affectedBlockPos = $_affectedBlock->getPosition();
											$this->affectedBlocks[World::blockHash($_affectedBlockPos->x, $_affectedBlockPos->y, $_affectedBlockPos->z)] = $_affectedBlock;
										}
									}
								}
							}
						}
					}
				}
			}
		}
		return true;
	}
}

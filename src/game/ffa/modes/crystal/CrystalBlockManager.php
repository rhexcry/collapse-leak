<?php

declare(strict_types=1);

namespace collapse\game\ffa\modes\crystal;

use collapse\player\CollapsePlayer;
use collapse\utils\InventoryUtils;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\ItemBlock;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelEvent;
use pocketmine\world\particle\BlockBreakParticle;
use pocketmine\world\sound\BlockBreakSound;
use pocketmine\world\World;
use function time;

final class CrystalBlockManager{

	/** @var array{block: Block, player: CollapsePlayer, time: int, progress: int, item: Item} */
	private array $blocks = [];

	private array $maxBlocks = [];

	public function __construct(
		private readonly Crystal $arena
	){
		$contents = $this->arena->getKit()->getContents();
		foreach($contents as $item){
			if(!$item instanceof ItemBlock){
				continue;
			}
			$this->maxBlocks[$item->getTypeId()] = InventoryUtils::countItems($contents, $item->getTypeId());
		}
		$this->arena->getPlugin()->getScheduler()->scheduleRepeatingTask(new CrystalBlockManagerUpdateTask($this), 20);
	}

	public function hasBlock(Block $block) : bool{
		$position = $block->getPosition();
		return isset($this->blocks[World::blockHash($position->getFloorX(), $position->getFloorY(), $position->getFloorZ())]);
	}

	public function onBlockPlace(CollapsePlayer $player, Block $block, Item $item) : void{
		$position = $block->getPosition();
		$this->blocks[World::blockHash($position->getFloorX(), $position->getFloorY(), $position->getFloorZ())] = [clone $block, $player, time(), clone $item];
		$player->getWorld()->broadcastPacketToViewers(
			$block->getPosition(),
			LevelEventPacket::create(LevelEvent::BLOCK_START_BREAK, (int) (65535 / 325), $block->getPosition())
		);
	}

	public function onBlockBreak(Block $block) : void{
		$position = $block->getPosition();
		$this->blocks[World::blockHash($position->getFloorX(), $position->getFloorY(), $position->getFloorZ())][0] = null;
	}

	public function onPlayerDie(CollapsePlayer $player) : void{
		foreach($this->blocks as $hash => $blockData){
			if($blockData[1] === $player){
				$this->blocks[$hash][1] = null;
				break;
			}
		}
	}

	public function update() : void{
		$currentTime = time();
		/**
		 * @var Block $block
		 * @var CollapsePlayer $player
		 * @var int $time
		 * @var Item $item
		 */
		foreach($this->blocks as $index => [$block, $player, $time, $item]){
			if(($currentTime - $time) > 15){
				if($block !== null){
					$blockPosition = $block->getPosition();
					if($blockPosition->getWorld() === null || !$blockPosition->getWorld()->isLoaded()){
						unset($this->blocks[$index]);
						break;
					}
					$blockPosition->getWorld()->broadcastPacketToViewers(
						$block->getPosition(),
						LevelEventPacket::create(LevelEvent::BLOCK_STOP_BREAK, 0, $blockPosition)
					);
					$blockPosition->getWorld()->setBlock($blockPosition, VanillaBlocks::AIR());
					$blockPosition->getWorld()->addParticle($blockPosition, new BlockBreakParticle($block));
					$blockPosition->getWorld()->addSound($blockPosition, new BlockBreakSound($block));
				}
				unset($this->blocks[$index]);
				if(
					$player !== null &&
					$player->isConnected() &&
					isset($this->maxBlocks[$item->getTypeId()]) &&
					InventoryUtils::countItems($player->getInventory(), $item->getTypeId()) < $this->maxBlocks[$item->getTypeId()] &&
					$player->isSurvival() &&
					$this->arena->getPlayerManager()->hasPlayer($player)
				){
					$player->getInventory()->addItem($item->setCount(1));
				}
				continue;
			}
			$block?->getPosition()->getWorld()?->broadcastPacketToViewers(
				$block->getPosition(),
				LevelEventPacket::create(LevelEvent::BLOCK_BREAK_SPEED, (int) (65535 / 325), $block->getPosition())
			);
		}
	}
}
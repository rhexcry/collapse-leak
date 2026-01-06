<?php

declare(strict_types=1);

namespace collapse\game\duel;

use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\world\World;
use function in_array;

final class DuelBlockManager{

	private array $blocks = [];

	private array $destroyableBlocks = [];

	private bool $allBlocksDestroyable = false;

	public function addDestroyableBlock(Block $block) : self{
		$this->destroyableBlocks[] = $block->getTypeId();
		return $this;
	}

	public function hasDestroyableBlock(Block $block) : bool{
		return in_array($block->getTypeId(), $this->destroyableBlocks, true);
	}

	public function isAllBlocksDestroyable() : bool{
		return $this->allBlocksDestroyable;
	}

	public function setAllBlocksDestroyable(bool $allBlocksDestroyable) : bool{
		return $this->allBlocksDestroyable;
	}

	public function hasBlock(Block $block) : bool{
		$position = $block->getPosition();
		return isset($this->blocks[World::blockHash($position->getFloorX(), $position->getFloorY(), $position->getFloorZ())]);
	}

	public function canBreakBlock(Block $block) : bool{
		if($this->hasDestroyableBlock($block)){
			return true;
		}
		return $this->hasBlock($block);
	}

	public function onBlockPlace(Block $block, Item $item) : void{
		$position = $block->getPosition();
		$this->blocks[World::blockHash($position->getFloorX(), $position->getFloorY(), $position->getFloorZ())] = [$block, clone $item];
	}

	public function onBlockBreak(Block $block) : void{
		$position = $block->getPosition();
		unset($this->blocks[World::blockHash($position->getFloorX(), $position->getFloorY(), $position->getFloorZ())]);
	}

	public function getBlockItem(Block $block) : ?Item{
		$position = $block->getPosition();
		return $this->blocks[World::blockHash($position->getFloorX(), $position->getFloorY(), $position->getFloorZ())][1] ?? null;
	}
}

<?php

declare(strict_types=1);

namespace collapse\game\duel\entity;

use collapse\game\Game;
use collapse\world\explosion\FireballFightExplosion;
use pocketmine\entity\NeverSavedWithChunkEntity;
use pocketmine\entity\object\PrimedTNT;
use pocketmine\event\entity\EntityPreExplodeEvent;
use pocketmine\world\Position;

final class TNT extends PrimedTNT implements NeverSavedWithChunkEntity{

	public function canSaveWithChunk() : bool{
		return false;
	}

	private ?Game $game = null;

	public function setGame(Game $game) : void{
		$this->game = $game;
	}

	public function getGame() : ?Game{
		return $this->game;
	}

	public function explode() : void{
		$ev = new EntityPreExplodeEvent($this, 5);
		$ev->call();
		if(!$ev->isCancelled()){
			$explosion = new FireballFightExplosion(Position::fromObject($this->location->add(0, ($this->size->getHeight() / 2), 0), $this->getWorld()), $ev->getRadius(), $this);
			if($ev->isBlockBreaking()){
				$explosion->explodeA();
			}
			$explosion->explodeB();
		}
	}
}

<?php

declare(strict_types=1);

namespace collapse\world\area;

use collapse\player\CollapsePlayer;
use pocketmine\math\AxisAlignedBB;
use pocketmine\world\World;
use function spl_object_id;

abstract class Area{

	private int $id;

	/** @var CollapsePlayer[] */
	private array $collidedPlayers = [];

	public function __construct(
		private readonly AxisAlignedBB $boundingBox,
		private readonly World $world
	){
		$this->id = spl_object_id($this);
	}

	public function getId() : int{
		return $this->id;
	}

	public function getBoundingBox() : AxisAlignedBB{
		return $this->boundingBox;
	}

	public function getWorld() : World{
		return $this->world;
	}

	public function isCollidesWith(AxisAlignedBB $boundingBox) : bool{
		return $this->boundingBox->intersectsWith($boundingBox, 0.001);
	}

	public function getCollidedPlayers() : array{
		return $this->collidedPlayers;
	}

	public function addCollidedPlayer(CollapsePlayer $player) : void{
		$this->collidedPlayers[$player->getId()] = $player;
	}

	public function removeCollidedPlayer(CollapsePlayer $player) : void{
		unset($this->collidedPlayers[$player->getId()]);
	}

	abstract public function onEnter(CollapsePlayer $player) : void;

	abstract public function onLeave(CollapsePlayer $player) : void;
}

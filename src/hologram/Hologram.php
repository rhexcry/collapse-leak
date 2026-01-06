<?php

declare(strict_types=1);

namespace collapse\hologram;

use pocketmine\entity\Location;

final class Hologram{

	private HologramEntity $entity;

	public function __construct(string $text, Location $location){
		$this->entity = new HologramEntity($location);
		$this->entity->setNameTag($text);
		$this->entity->spawnToAll();
	}

	public function getId() : int{
		return $this->entity->getId();
	}

	public function close() : void{
		$this->entity->close();
	}
}

<?php

declare(strict_types=1);

namespace collapse\game\duel\modes\skywars\island;

use collapse\game\duel\modes\skywars\island\config\IslandConfig;

final class Island{

	private array $chests = [];

	public function __construct(
		protected IslandConfig $config
	){
	}

	public function getId() : string{
		return $this->config->getId();
	}

	public function getType() : IslandType{
		return $this->config->getType();
	}

	public function addChest(IslandChest $chest) : void{
		$this->chests[] = $chest;
	}

	public function getChests() : array{
		return $this->chests;
	}
}

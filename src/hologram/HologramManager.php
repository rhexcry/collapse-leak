<?php

declare(strict_types=1);

namespace collapse\hologram;

final class HologramManager{

	/** @var Hologram[]  */
	private array $holograms = [];

	public function add(Hologram $hologram) : void{
		$this->holograms[$hologram->getId()] = $hologram;
	}

	public function remove(Hologram $hologram) : void{
		if(isset($this->holograms[$id = $hologram->getId()])){
			$this->holograms[$id]->close();
			unset($this->holograms[$id]);
		}
	}
}

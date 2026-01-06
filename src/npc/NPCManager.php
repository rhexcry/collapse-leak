<?php

declare(strict_types=1);

namespace collapse\npc;

use collapse\Practice;
use pocketmine\scheduler\ClosureTask;

final class NPCManager{

	/** @var array<int, CollapseNPC> */
	private array $npcMap = [];

	public function __construct(private readonly Practice $plugin){
		$this->plugin->getServer()->getPluginManager()->registerEvents(new NPCListener($this), $this->plugin);
		$this->plugin->getScheduler()->scheduleDelayedRepeatingTask(new ClosureTask(function() : void{
			foreach($this->npcMap as $npc){
				if($npc instanceof UpdatableNPC){
					$npc->update();
				}
			}
		}), 20, 20);
	}

	public function add(CollapseNPC $npc) : void{
		if(isset($this->npcMap[$id = $npc->getId()])){
			throw new \RuntimeException("NPC with id \"$id\" already exists.");
		}

		$this->npcMap[$npc->getId()] = $npc;
		$npc->spawnToAll();
	}

	public function remove(CollapseNPC $npc) : void{
		if(!isset($this->npcMap[$id = $npc->getId()])){
			throw new \RuntimeException("NPC with id \"$id\" does not exist.");
		}

		$npc->close();
		unset($this->npcMap[$id]);
	}

	public function getAll() : array{
		return $this->npcMap;
	}

	public function close() : void{
		foreach($this->npcMap as $npc){
			$npc->close();
		}
	}
}

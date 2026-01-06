<?php

declare(strict_types=1);

namespace collapse\npc\location;

use pocketmine\entity\Location;
use pocketmine\Server;

enum DefaultNPCLocationDefinitions{

	#[NPCLocationAttribute(-43.5, -37, 6.5, 'world', 220, 0)]
	case Duels_FireballFight;

	#[NPCLocationAttribute(-46.5, -37, 2.5, 'world', 180, 0)]
	case Duels;

	#[NPCLocationAttribute(-46.5, -37, -1.5, 'world', 180, 0)]
	case FFA;

	#[NPCLocationAttribute(-43.5, -37, -5.5, 'world', 180, 0)]
	case FFA_Build;

	#[NPCLocationAttribute(-16, -35.5, 16, 'world', 180, 0)]
	case Collapse;

	public function getLocation() : Location{
		$reflection = new \ReflectionEnumUnitCase($this::class, $this->name);
		$attributes = $reflection->getAttributes(NPCLocationAttribute::class);

		if(empty($attributes)){
			throw new \RuntimeException('No location attribute for NPC ' . $this->name);
		}

		/** @var NPCLocationAttribute $attr */
		$attr = $attributes[0]->newInstance();

		$world = Server::getInstance()->getWorldManager()->getWorldByName($attr->world);

		if($world === null){
			throw new \RuntimeException('World  "' . $attr->world . '" not found for NPC ' . $this->name);
		}

		return new Location(
			$attr->x,
			$attr->y,
			$attr->z,
			$world,
			$attr->yaw,
			$attr->pitch
		);
	}
}

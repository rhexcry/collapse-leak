<?php

declare(strict_types=1);

namespace collapse\world\explosion;

use collapse\Practice;
use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByBlockEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\item\VanillaItems;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\particle\HugeExplodeSeedParticle;
use pocketmine\world\sound\ExplodeSound;
use function ceil;
use function floor;
use function min;
use function mt_rand;

class FireballFightExplosion extends SimpleExplosion{

	public function explodeB() : bool{
		$source = (new Vector3($this->source->x, $this->source->y, $this->source->z))->floor();
		$yield = min(100, (1 / $this->radius) * 100);

		if($this->what instanceof Entity){
			$ev = new EntityExplodeEvent($this->what, $this->source, $this->affectedBlocks, $yield);
			$ev->call();
			if($ev->isCancelled()){
				return false;
			}else{
				$yield = $ev->getYield();
				$this->affectedBlocks = $ev->getBlockList();
			}
		}

		$explosionSize = $this->radius * 2;
		$minX = (int) floor($this->source->x - $explosionSize - 1);
		$maxX = (int) ceil($this->source->x + $explosionSize + 1);
		$minY = (int) floor($this->source->y - $explosionSize - 1);
		$maxY = (int) ceil($this->source->y + $explosionSize + 1);
		$minZ = (int) floor($this->source->z - $explosionSize - 1);
		$maxZ = (int) ceil($this->source->z + $explosionSize + 1);

		$explosionBB = new AxisAlignedBB($minX, $minY, $minZ, $maxX, $maxY, $maxZ);

		$list = $this->world->getNearbyEntities($explosionBB, $this->what instanceof Entity ? $this->what : null);
		foreach($list as $entity){
			$entityPos = $entity->getPosition();
			$distance = $entityPos->distance($this->source) / $explosionSize;

			if($distance <= 1){
				$damage = 1;

				if($this->what instanceof Entity){
					$ev = new EntityDamageByEntityEvent($this->what, $entity, EntityDamageEvent::CAUSE_ENTITY_EXPLOSION, $damage);
				}elseif($this->what instanceof Block){
					$ev = new EntityDamageByBlockEvent($this->what, $entity, EntityDamageEvent::CAUSE_BLOCK_EXPLOSION, $damage);
				}else{
					$ev = new EntityDamageEvent($entity, EntityDamageEvent::CAUSE_BLOCK_EXPLOSION, $damage);
				}

				$motion = $entity->getLocation()->subtractVector($this->source)->normalize();
				$motion->x *= 1.3;
				$motion->y *= 1.5;
				$motion->z *= 1.3;

				$entity->attack($ev);
				Practice::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($entity, $motion) : void {
					$entity?->setMotion($entity->getMotion()->addVector($motion));
				}), 1);
			}
		}

		$air = VanillaItems::AIR();
		$airBlock = VanillaBlocks::AIR();

		foreach($this->affectedBlocks as $block){
			$pos = $block->getPosition();
			if(mt_rand(0, 100) < $yield){
				foreach($block->getDrops($air) as $drop){
					$this->world->dropItem($pos->add(0.5, 0.5, 0.5), $drop);
				}
			}
			if(($tile = $this->world->getTileAt($pos->x, $pos->y, $pos->z)) !== null){
				$tile->onBlockDestroyed();
			}
			$this->world->setBlockAt($pos->x, $pos->y, $pos->z, $airBlock);
		}

		$this->world->addParticle($source, new HugeExplodeSeedParticle());
		$this->world->addSound($source, new ExplodeSound());
		return true;
	}
}

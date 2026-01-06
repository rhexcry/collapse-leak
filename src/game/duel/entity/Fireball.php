<?php

declare(strict_types=1);

namespace collapse\game\duel\entity;

use collapse\game\Game;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\NeverSavedWithChunkEntity;
use pocketmine\entity\projectile\Throwable;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\player\Player;

final class Fireball extends Throwable implements NeverSavedWithChunkEntity{

	private const int DEFAULT_DESPAWN_DELAY = 20 * 10;

	private ?Game $game = null;

	private int $despawnDelay = self::DEFAULT_DESPAWN_DELAY;

	private bool $redirect = false;

	public static function getNetworkTypeId() : string{
		return EntityIds::FIREBALL;
	}

	protected function getInitialDragMultiplier() : float{
		return 0.05;
	}

	protected function getInitialGravity() : float{
		return 0.001;
	}

	protected function getInitialSizeInfo() : EntitySizeInfo{
		return new EntitySizeInfo(1, 1);
	}

	public function attack(EntityDamageEvent $source) : void{
		if($source instanceof EntityDamageByEntityEvent && $this->ticksLived > 5 && $source->getCause() === EntityDamageEvent::CAUSE_ENTITY_ATTACK && !$this->redirect){
			$attacker = $source->getDamager();
			if(!($attacker instanceof Player && $attacker->isSurvival())){
				return;
			}
			$this->setMotion($attacker->getDirectionVector()->multiply(1.5));
			$this->redirect = true;
		}
	}

	public function entityBaseTick(int $tickDiff = 1) : bool{
		$hasUpdate = parent::entityBaseTick($tickDiff);

		$this->despawnDelay -= $tickDiff;
		if($this->despawnDelay <= 0){
			$this->flagForDespawn();
			return true;
		}

		return $hasUpdate;
	}

	protected function tryChangeMovement() : void{

	}

	public function setGame(Game $game) : void{
		$this->game = $game;
	}

	public function getGame() : ?Game{
		return $this->game;
	}

	protected function syncNetworkData(EntityMetadataCollection $properties) : void{
		parent::syncNetworkData($properties);
		$properties->setGenericFlag(EntityMetadataFlags::ONFIRE, true);
	}
}

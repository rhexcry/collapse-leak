<?php

declare(strict_types=1);

namespace collapse\game\duel\item;

use collapse\game\duel\entity\Fireball as FireballEntity;
use collapse\i18n\CollapseTranslationFactory;
use collapse\item\default\CollapseProjectileItemTrait;
use collapse\item\TranslatableItem;
use collapse\player\CollapsePlayer;
use pocketmine\entity\Location;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\ProjectileItem;
use pocketmine\player\Player;

final class Fireball extends ProjectileItem implements TranslatableItem{
	use CollapseProjectileItemTrait;

	public function __construct(){
		parent::__construct(new ItemIdentifier(ItemTypeIds::FIRE_CHARGE));
	}

	public function getThrowForce() : float{
		return 1.5;
	}

	/**
	 * @param CollapsePlayer $thrower
	 */
	protected function createEntity(Location $location, Player $thrower) : FireballEntity{
		$entity = new FireballEntity($location, $thrower);
		$entity->setGame($thrower->getGame());
		return $entity;
	}

	public function translate(CollapsePlayer $player) : self{
		return $this->setCustomName($player->getProfile()->getTranslator()->translate(CollapseTranslationFactory::duels_item_fireball()));
	}
}

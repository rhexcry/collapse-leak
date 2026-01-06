<?php

declare(strict_types=1);

namespace collapse\game\duel\item;

use collapse\i18n\CollapseTranslationFactory;
use collapse\item\TranslatableItem;
use collapse\player\CollapsePlayer;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Living;
use pocketmine\item\Food;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;

final class GoldenHead extends Food implements TranslatableItem{

	public function __construct(){
		parent::__construct(new ItemIdentifier(ItemTypeIds::GOLDEN_APPLE));
	}

	public function getFoodRestore() : int{
		return 0;
	}

	public function getSaturationRestore() : float{
		return 0.0;
	}

	public function requiresHunger() : bool{
		return false;
	}

	public function onConsume(Living $consumer) : void{
		$consumer->getEffects()->add(new EffectInstance(VanillaEffects::SPEED(), 20 * 8));
		$consumer->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 20 * 5, 2));
	}

	public function translate(CollapsePlayer $player) : self{
		return $this->setCustomName($player->getProfile()->getTranslator()->translate(CollapseTranslationFactory::duels_item_golden_head()));
	}
}

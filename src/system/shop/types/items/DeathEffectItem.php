<?php

declare(strict_types=1);

namespace collapse\system\shop\types\items;

use collapse\cosmetics\effects\death\DeathEffectType;
use collapse\player\profile\Profile;
use collapse\system\shop\types\ProfileCollectibleShopItem;
use collapse\system\shop\types\ShopCategoryName;
use collapse\system\shop\types\ShopItem;
use pocketmine\lang\Translatable;

final readonly class DeathEffectItem extends ShopItem implements ProfileCollectibleShopItem{

	public function __construct(
		private DeathEffectType $deathEffect,
		Translatable|string $name,
		Translatable|string $description,
		int $price,
		string $iconPath
	){
		parent::__construct('death_effect_' . $this->deathEffect->value, ShopCategoryName::DeathEffects, $name, $description, $price, $iconPath);
	}

	public function getDeathEffect() : DeathEffectType{
		return $this->deathEffect;
	}

	public function onPurchaseSuccess(Profile $profile) : void{
		$profile->addPurchasedDeathEffect($this->deathEffect);
	}

	public function isCollected(Profile $profile) : bool{
		return $profile->hasPurchasedDeathEffect($this->deathEffect);
	}
}

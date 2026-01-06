<?php

declare(strict_types=1);

namespace collapse\system\shop\types\items;

use collapse\cosmetics\capes\Cape;
use collapse\player\profile\Profile;
use collapse\system\shop\types\ProfileCollectibleShopItem;
use collapse\system\shop\types\ShopCategoryName;
use collapse\system\shop\types\ShopItem;
use pocketmine\lang\Translatable;

final readonly class CapeItem extends ShopItem implements ProfileCollectibleShopItem{

	public function __construct(
		private Cape $cape,
		Translatable|string $name,
		Translatable|string $description,
		int $price,
		string $iconPath
	){
		parent::__construct('cape_' . $this->cape->value, ShopCategoryName::Capes, $name, $description, $price, $iconPath);
	}

	public function getCape() : Cape{
		return $this->cape;
	}

	public function onPurchaseSuccess(Profile $profile) : void{
		$profile->addPurchasedCape($this->cape);
	}

	public function isCollected(Profile $profile) : bool{
		return $profile->hasPurchasedCape($this->cape);
	}
}

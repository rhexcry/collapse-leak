<?php

declare(strict_types=1);

namespace collapse\system\shop\types\items;

use collapse\player\profile\Profile;
use collapse\player\rank\Rank;
use collapse\system\shop\types\ShopCategoryName;
use collapse\system\shop\types\ShopItem;
use pocketmine\lang\Translatable;

final readonly class RankItem extends ShopItem{

	public function __construct(
		private Rank $rank,
		Translatable|string $name,
		Translatable|string $description,
		int $price,
		string $iconPath
	){
		parent::__construct($this->rank->value, ShopCategoryName::Ranks, $name, $description, $price, $iconPath);
	}

	public function getRank() : Rank{
		return $this->rank;
	}

	public function onTryPurchase(Profile $profile) : bool{
		return $profile->getRank()->getPriority() < $this->rank->getPriority();
	}
}

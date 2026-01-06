<?php

declare(strict_types=1);

namespace collapse\system\shop\event;

use collapse\player\profile\Profile;
use collapse\system\shop\types\ShopItem;

final class PlayerPurchaseEvent extends ShopEvent{

	public function __construct(
		private readonly Profile $profile,
		private readonly ShopItem $shopItem
	){}

	public function getProfile() : Profile{
		return $this->profile;
	}

	public function getShopItem() : ShopItem{
		return $this->shopItem;
	}
}

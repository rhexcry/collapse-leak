<?php

declare(strict_types=1);

namespace collapse\system\shop\types;

use collapse\player\profile\Profile;
use collapse\Practice;
use pocketmine\lang\Translatable;

abstract readonly class ShopItem{

	private int|float $priceInRub;

	private bool $onlyServer;

	public function __construct(
		private string $id,
		private ShopCategoryName $categoryId,
		private Translatable|string $name,
		private Translatable|string $description,
		private int $price,
		private string $iconPath
	){
		$this->priceInRub = $price / 20;
		$this->onlyServer = false;
	}

	public function getId() : string{
		return $this->id;
	}

	public function getCategory() : ShopCategory{
		return Practice::getInstance()->getShopManager()->getCategory($this->categoryId);
	}

	public function getCategoryId() : ShopCategoryName{
		return $this->categoryId;
	}

	public function getPrice() : int{
		return $this->price;
	}

	public function getPriceInRub() : int|float{
		return $this->priceInRub;
	}

	public function getName() : string|Translatable{
		return $this->name;
	}

	public function getDescription() : Translatable{
		return $this->description;
	}

	public function getIconPath() : string{
		return $this->iconPath;
	}

	public function isOnlyServer() : bool{
		return $this->onlyServer;
	}

	public function onTryPurchase(Profile $profile) : bool{
		return true;
	}

	public function onPurchaseFailed(Profile $profile) : void{

	}

	public function onPurchaseSuccess(Profile $profile) : void{

	}
}

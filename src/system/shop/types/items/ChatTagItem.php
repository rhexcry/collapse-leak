<?php

declare(strict_types=1);

namespace collapse\system\shop\types\items;

use collapse\cosmetics\tags\ChatTag;
use collapse\player\profile\Profile;
use collapse\system\shop\types\ProfileCollectibleShopItem;
use collapse\system\shop\types\ShopCategoryName;
use collapse\system\shop\types\ShopItem;
use pocketmine\lang\Translatable;

final readonly class ChatTagItem extends ShopItem implements ProfileCollectibleShopItem{

	public function __construct(
		private ChatTag $chatTag,
		Translatable|string $name,
		Translatable|string $description,
		int $price,
		string $iconPath
	){
		parent::__construct('chat_tag_' . $this->chatTag->value, ShopCategoryName::ChatTags, $name, $description, $price, $iconPath);
	}

	public function getChatTag() : ChatTag{
		return $this->chatTag;
	}

	public function onPurchaseSuccess(Profile $profile) : void{
		$profile->addPurchasedChatTag($this->chatTag);
	}

	public function isCollected(Profile $profile) : bool{
		return $profile->hasPurchasedChatTag($this->chatTag);
	}
}

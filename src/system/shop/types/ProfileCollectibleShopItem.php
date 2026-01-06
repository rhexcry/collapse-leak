<?php

declare(strict_types=1);

namespace collapse\system\shop\types;

use collapse\player\profile\Profile;

interface ProfileCollectibleShopItem{

	public function isCollected(Profile $profile) : bool;
}

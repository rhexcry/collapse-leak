<?php

declare(strict_types=1);

namespace collapse\system\shop\types;

use pocketmine\lang\Translatable;

readonly class ShopCategory{
	public function __construct(
		private string $id,
		private Translatable $name,
		private Translatable $description,
		private string $iconPath
	){}

	public function getId() : string{
		return $this->id;
	}

	public function getName() : Translatable{
		return $this->name;
	}

	public function getDescription() : Translatable{
		return $this->description;
	}

	public function getIconPath() : string{
		return $this->iconPath;
	}
}

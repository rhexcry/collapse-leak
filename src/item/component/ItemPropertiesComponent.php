<?php

declare(strict_types=1);

namespace collapse\item\component;

use pocketmine\nbt\tag\CompoundTag;

final readonly class ItemPropertiesComponent extends ItemComponent{

	private const string COMPONENT = 'item_properties';

	private const string ITEM_PROPERTY_ICON = 'minecraft:icon';
	private const string ITEM_PROPERTY_ICON_TEXTURES = 'textures';
	private const string ITEM_PROPERTY_ICON_LEGACY_ID = 'legacy_id';
	private const string ITEM_PROPERTY_USE_DURATION = 'use_duration';

	public static function create(string $texture, string $legacyId, int $useDuration = 0) : self{
		return new self($texture, $legacyId, $useDuration);
	}

	private CompoundTag $nbt;

	private function __construct(
		private string $texture,
		private string $legacyId,
		private int $useDuration = 0
	){
		$this->nbt = CompoundTag::create();
		$this->nbt->setTag(self::ITEM_PROPERTY_ICON, CompoundTag::create()
			->setTag(self::ITEM_PROPERTY_ICON_TEXTURES, CompoundTag::create()->setString('default', $this->texture))
			->setString(self::ITEM_PROPERTY_ICON_LEGACY_ID, $this->legacyId)
		);
		$this->nbt->setInt(self::ITEM_PROPERTY_USE_DURATION, $this->useDuration);
	}

	public function write(CompoundTag $nbt) : void{
		$nbt->setTag(self::COMPONENT, $this->nbt);
	}
}

<?php

declare(strict_types=1);

namespace collapse\item\component;

use pocketmine\nbt\tag\CompoundTag;

final readonly class ItemComponents{

	private const string NBT_COMPONENTS_NAME = 'components';
	private const string COMPONENT_IDENTIFIER = 'minecraft:identifier';

	public static function create(int $runtimeId) : self{
		return new self($runtimeId);
	}

	private CompoundTag $nbt;

	private function __construct(
		private int $runtimeId
	){
		$this->nbt = CompoundTag::create();
	}

	public function with(ItemComponent $component) : self{
		$component->write($this->nbt);
		return $this;
	}

	public function toNbt() : CompoundTag{
		return CompoundTag::create()
			->setTag(self::NBT_COMPONENTS_NAME, $this->nbt)
			->setShort(self::COMPONENT_IDENTIFIER, $this->runtimeId);
	}
}

<?php

declare(strict_types=1);

namespace collapse\item\component;

use pocketmine\nbt\tag\CompoundTag;

final readonly class DisplayNameComponent extends ItemComponent{

	private const string COMPONENT = 'minecraft:display_name';

	public static function create(string $displayName) : self{
		return new self($displayName);
	}

	private function __construct(
		private string $displayName
	){}

	public function write(CompoundTag $nbt) : void{
		$nbt->setTag(self::COMPONENT, CompoundTag::create()->setString('value', $this->displayName));
	}
}

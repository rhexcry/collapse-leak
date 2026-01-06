<?php

declare(strict_types=1);

namespace collapse\item\component;

use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;

final readonly class RenderOffsetsComponent extends ItemComponent{

	private const string COMPONENT = 'minecraft:render_offsets';

	private const string OFFSET_MAIN_HAND = 'main_hand';
	private const string OFFSET_OFF_HAND = 'off_hand';

	private const string OFFSET_FIRST_PERSON = 'first_person';
	private const string OFFSET_THIRD_PERSON = 'third_person';

	private const string OFFSET_SCALE = 'scale';

	private const array BASE_OFFSET = [0.075, 0.125, 0.075];

	public static function create() : self{
		return new self();
	}

	public static function calculate(int $size) : Vector3{
		[$x, $y, $z] = self::BASE_OFFSET;
		return new Vector3(
			$x / ($size / 16),
			$y / ($size / 16),
			$z / ($size / 16)
		);
	}

	private CompoundTag $nbt;

	private function __construct(){
		$this->nbt = CompoundTag::create();
	}

	public function mainHandFirstPersonScale(Vector3 $scale) : self{
		$this->nbt->setTag(self::OFFSET_MAIN_HAND, ($this->nbt->getTag(self::OFFSET_MAIN_HAND) ?? CompoundTag::create())
			->setTag(self::OFFSET_FIRST_PERSON, CompoundTag::create()
				->setTag(self::OFFSET_SCALE, new ListTag([
					new FloatTag($scale->x),
					new FloatTag($scale->y),
					new FloatTag($scale->z)
				]))
			)
		);
		return $this;
	}

	public function mainHandThirdPersonScale(Vector3 $scale) : self{
		$this->nbt->setTag(self::OFFSET_MAIN_HAND, ($this->nbt->getTag(self::OFFSET_MAIN_HAND) ?? CompoundTag::create())
			->setTag(self::OFFSET_THIRD_PERSON, CompoundTag::create()
				->setTag(self::OFFSET_SCALE, new ListTag([
					new FloatTag($scale->x),
					new FloatTag($scale->y),
					new FloatTag($scale->z)
				]))
			)
		);
		return $this;
	}

	public function offHandFirstPersonScale(Vector3 $scale) : self{
		$this->nbt->setTag(self::OFFSET_OFF_HAND, ($this->nbt->getTag(self::OFFSET_OFF_HAND) ?? CompoundTag::create())
			->setTag(self::OFFSET_FIRST_PERSON, CompoundTag::create()
				->setTag(self::OFFSET_SCALE, new ListTag([
					new FloatTag($scale->x),
					new FloatTag($scale->y),
					new FloatTag($scale->z)
				]))
			)
		);
		return $this;
	}

	public function offHandThirdPersonScale(Vector3 $scale) : self{
		$this->nbt->setTag(self::OFFSET_OFF_HAND, ($this->nbt->getTag(self::OFFSET_OFF_HAND) ?? CompoundTag::create())
			->setTag(self::OFFSET_THIRD_PERSON, CompoundTag::create()
				->setTag(self::OFFSET_SCALE, new ListTag([
					new FloatTag($scale->x),
					new FloatTag($scale->y),
					new FloatTag($scale->z)
				]))
			)
		);
		return $this;
	}

	public function write(CompoundTag $nbt) : void{
		$nbt->setTag(self::COMPONENT, $this->nbt);
	}
}

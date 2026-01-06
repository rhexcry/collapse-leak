<?php

declare(strict_types=1);

namespace collapse\utils;

use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\ListTag;
use JsonSerializable;

final class SerializableVector3 implements JsonSerializable{

	public function __construct(
		private float $x,
		private float $y,
		private float $z
	){
	}

	public static function fromVector3(Vector3 $vector) : self{
		return new self($vector->x, $vector->y, $vector->z);
	}

	public function toVector3() : Vector3{
		return new Vector3($this->x, $this->y, $this->z);
	}

	public function getX() : float{
		return $this->x;
	}

	public function getY() : float{
		return $this->y;
	}

	public function getZ() : float{
		return $this->z;
	}

	public function equals(self $other) : bool{
		return $this->x === $other->x &&
			$this->y === $other->y &&
			$this->z === $other->z;
	}

	public function add(float $x, float $y, float $z) : self{
		return new self($this->x + $x, $this->y + $y, $this->z + $z);
	}

	public function subtract(float $x, float $y, float $z) : self{
		return new self($this->x - $x, $this->y - $y, $this->z - $z);
	}

	public function multiply(float $multiplier) : self{
		return new self($this->x * $multiplier, $this->y * $multiplier, $this->z * $multiplier);
	}

	public function distance(self $other) : float{
		$dx = $this->x - $other->x;
		$dy = $this->y - $other->y;
		$dz = $this->z - $other->z;

		return sqrt($dx * $dx + $dy * $dy + $dz * $dz);
	}

	public function __toString() : string{
		return sprintf("SerializableVector3(x=%.2f, y=%.2f, z=%.2f)", $this->x, $this->y, $this->z);
	}

	public function toNBT() : CompoundTag{
		return CompoundTag::create()
			->setTag("Pos", new ListTag([
				new DoubleTag($this->x),
				new DoubleTag($this->y),
				new DoubleTag($this->z)
			]));
	}

	public static function fromNBT(CompoundTag $tag) : self{
		$pos = $tag->getListTag("Pos");
		if($pos === null || $pos->count() !== 3){
			throw new \InvalidArgumentException('Invalid NBT data for SerializableVector3');
		}

		return new self(
			(float) $pos->get(0)->getValue(),
			(float) $pos->get(1)->getValue(),
			(float) $pos->get(2)->getValue()
		);
	}

	public function jsonSerialize() : array{
		return [
			'x' => $this->x,
			'y' => $this->y,
			'z' => $this->z
		];
	}

	public static function fromJson(array $data) : self{
		return new self(
			(float) ($data['x'] ?? 0),
			(float) ($data['y'] ?? 0),
			(float) ($data['z'] ?? 0)
		);
	}

	public function toConfigArray() : array{
		return [$this->x, $this->y, $this->z];
	}

	public static function fromConfigArray(array $data) : self{
		if(count($data) !== 3){
			throw new \InvalidArgumentException('Config array must contain exactly 3 elements');
		}

		return new self(
			(float) $data[0],
			(float) $data[1],
			(float) $data[2]
		);
	}

	public function serialize() : string{
		return implode(':', [$this->x, $this->y, $this->z]);
	}

	public static function unserialize(string $data) : self{
		$parts = explode(':', $data);
		if(count($parts) !== 3){
			throw new \InvalidArgumentException('Invalid serialized data');
		}

		return new self(
			(float) $parts[0],
			(float) $parts[1],
			(float) $parts[2]
		);
	}

	public static function fromArray(array $data) : self{
		return new self(
			(float) ($data['x'] ?? $data[0] ?? 0),
			(float) ($data['y'] ?? $data[1] ?? 0),
			(float) ($data['z'] ?? $data[2] ?? 0)
		);
	}

	public function toArray() : array{
		return ['x' => $this->x, 'y' => $this->y, 'z' => $this->z];
	}

	public function toSimpleArray() : array{
		return [$this->x, $this->y, $this->z];
	}
}
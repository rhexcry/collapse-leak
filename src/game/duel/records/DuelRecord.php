<?php

declare(strict_types=1);

namespace collapse\game\duel\records;

use collapse\game\duel\types\DuelMode;
use collapse\game\duel\types\DuelType;
use collapse\player\CollapsePlayer;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\item\Item;
use pocketmine\lang\Translatable;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;
use function array_map;
use function in_array;

class DuelRecord{

	final public static function formatPotionsEffects(CollapsePlayer $player) : array{
		return array_map(static function(EffectInstance $effect) : array{
			$name = $effect->getType()->getName();
			return [
				$name instanceof Translatable ? $name->getText() : $name,
				$effect->getAmplifier(),
				$effect->getDuration()
			];
		}, $player->getEffects()->all());
	}

	private bool $hasDuration = true;

	/**
	 * @param Item[][] $inventories
	 */
	public function __construct(
		private readonly DuelMode $mode,
		private readonly DuelType $type,
		private array $winners,
		private array $losers,
		private readonly int $time,
		private int $duration,
		private array $inventories,
		private array $statistics,
		private array $potionEffects,
		private array $eloUpdates = []
	){}

	public function getMode() : DuelMode{
		return $this->mode;
	}

	public function getType() : DuelType{
		return $this->type;
	}

	public function getInventories() : array{
		return $this->inventories;
	}

	public function getInventory(string $xuid) : array{
		return $this->inventories[$xuid] ?? [];
	}

	public function getStatistics() : array{
		return $this->statistics;
	}

	/**
	 * @param string[] $winners XUIDs
	 */
	public function setWinners(array $winners) : void{
		$this->winners = array_map(static fn(mixed $xuid) : string => (string) $xuid, $winners);
	}

	public function getWinners() : array{
		return $this->winners;
	}

	public function hasWinner(string $xuid) : bool{
		return in_array($xuid, $this->winners, true);
	}

	/**
	 * @param string[] $losers XUIDs
	 */
	public function setLosers(array $losers) : void{
		$this->losers = array_map(static fn(mixed $xuid) : string => (string) $xuid, $losers);
	}

	public function getLosers() : array{
		return $this->losers;
	}

	public function hasLoser(string $xuid) : bool{
		return in_array($xuid, $this->losers, true);
	}

	public function getTime() : int{
		return $this->time;
	}

	public function setDurationEnabled(bool $hasDuration) : void{
		$this->hasDuration = $hasDuration;
	}

	public function isDurationEnabled() : bool{
		return $this->hasDuration;
	}

	public function addDuration() : void{
		++$this->duration;
	}

	public function getDuration() : int{
		return $this->duration;
	}

	public function saveInventory(string $xuid, array $contents) : void{
		$this->inventories[$xuid] = $contents;
	}

	public function setStatistics(array $statistics) : void{
		$this->statistics = $statistics;
	}

	public function setPotionEffects(string $xuid, array $potionEffects) : void{
		$this->potionEffects[$xuid] = $potionEffects;
	}

	public function getPotionEffects(string $xuid) : array{
		return $this->potionEffects[$xuid];
	}

	public function setEloUpdates(array $eloUpdates) : void{
		$this->eloUpdates = $eloUpdates;
	}

	public function getEloUpdates() : array{
		return $this->eloUpdates;
	}
	public function export() : array{
		return [
			'mode' => $this->mode->value,
			'type' => $this->type->value,
			'winners' => $this->winners,
			'losers' => $this->losers,
			'time' => $this->time,
			'duration' => $this->duration,
			'inventories' => array_map(static fn(array $inventory) : array => array_map(static function(Item $item) : array{
				return [
					'typeId' => $item->getTypeId(),
					'nbt' => (new LittleEndianNbtSerializer())->write(new TreeRoot($item->nbtSerialize()))
				];
			}, $inventory), $this->inventories),
			'statistics' => $this->statistics,
			'potionEffects' => $this->potionEffects,
			'eloUpdates' => $this->eloUpdates
		];
	}
}

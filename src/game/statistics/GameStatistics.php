<?php

declare(strict_types=1);

namespace collapse\game\statistics;

use collapse\player\CollapsePlayer;
use pocketmine\lang\Translatable;

final class GameStatistics{

	public const string DAMAGE_DEALT = 'damage_dealt';
	public const string HEALTH_REGENERATED = 'health_regenerated';
	public const string COMBO = 'combo';
	public const string MAX_COMBO = 'max_combo';
	public const string HITS = 'hits';
	public const string CRITICAL_HITS = 'critical_hits';
	public const string TOTAL_POTIONS = 'total_potions';
	public const string THROW_POTIONS = 'throw_potions';
	public const string HEALTH = 'health';
	public const string HUNGER = 'hunger';

	/** @var (int|float)[] */
	private array $data = [];

	public function __construct(
		private readonly string $id,
		private readonly ?Translatable $translation
	){}

	public function getId() : string{
		return $this->id;
	}

	public function canBeDisplayed() : bool{
		return $this->translation !== null;
	}

	public function add(CollapsePlayer $player, int|float $value) : void{
		if(!isset($this->data[$player->getXuid()])){
			$this->data[$player->getXuid()] = 0;
		}
		$this->data[$player->getXuid()] += $value;
	}

	public function set(CollapsePlayer $player, int|float $value) : void{
		$this->data[$player->getXuid()] = $value;
	}

	public function get(CollapsePlayer|string $player, int|float $default = 0) : int|float{
		return $this->data[$player instanceof CollapsePlayer ? $player->getXuid() : $player] ?? $default;
	}

	public function getData() : array{
		return $this->data;
	}

	public function translate(CollapsePlayer $player) : string{
		return $player->getProfile()->getTranslator()->translate($this->translation);
	}
}

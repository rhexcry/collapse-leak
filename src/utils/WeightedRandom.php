<?php

declare(strict_types=1);

namespace collapse\utils;

use pocketmine\utils\Random;

/**
 * @template T
 */
final class WeightedRandom{

	/** @var array<int, array{value: T, weight: int}> */
	private array $entries = [];
	private int $totalWeight = 0;

	/**
	 * @param T $value
	 */
	public function add($value, int $weight = 1) : void{
		if($weight <= 0){
			throw new \InvalidArgumentException('Weight must be positive');
		}

		$this->entries[] = [
			'value' => $value,
			'weight' => $weight
		];

		$this->totalWeight += $weight;
	}

	/**
	 * @return T
	 */
	public function get(Random $random){
		if($this->totalWeight === 0){
			throw new \RuntimeException('No entries in weighted random');
		}

		$randomValue = $random->nextBoundedInt($this->totalWeight);
		$currentWeight = 0;

		foreach($this->entries as $entry){
			$currentWeight += $entry['weight'];

			if($randomValue < $currentWeight){
				return $entry['value'];
			}
		}

		throw new \RuntimeException('Failed to select weighted random value');
	}

	/**
	 * @return array<T>
	 */
	public function getMultiple(Random $random, int $count) : array{
		if($count <= 0){
			throw new \InvalidArgumentException('Count must be positive');
		}

		$results = [];
		for($i = 0; $i < $count; $i++){
			$results[] = $this->get($random);
		}

		return $results;
	}

	public function getTotalWeight() : int{
		return $this->totalWeight;
	}

	public function getEntryCount() : int{
		return count($this->entries);
	}

	/**
	 * @return array<int, array{value: T, weight: int}>
	 */
	public function getEntries() : array{
		return $this->entries;
	}

	public function clear() : void{
		$this->entries = [];
		$this->totalWeight = 0;
	}

	public function isEmpty() : bool{
		return $this->totalWeight === 0;
	}

	/**
	 * @param array<T> $values
	 * @param array<int> $weights
	 */
	public static function fromArrays(array $values, array $weights) : self{
		if(count($values) !== count($weights)){
			throw new \InvalidArgumentException('Values and weights arrays must have same length');
		}

		$instance = new self();
		foreach($values as $index => $value){
			$instance->add($value, $weights[$index]);
		}

		return $instance;
	}

	/**
	 * @param array<array{value: T, weight: int}> $entries
	 */
	public static function fromEntries(array $entries) : self{
		$instance = new self();
		foreach($entries as $entry){
			$instance->add($entry['value'], $entry['weight']);
		}

		return $instance;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function __debugInfo() : array{
		return [
			'totalWeight' => $this->totalWeight,
			'entryCount' => $this->getEntryCount(),
			'entries' => $this->entries
		];
	}
}

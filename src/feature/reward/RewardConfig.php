<?php

declare(strict_types=1);

namespace collapse\feature\reward;

readonly class RewardConfig{

	public function __construct(
		public RewardType $type,
		public mixed $value = null
	){
	}

	public function getType() : RewardType{
		return $this->type;
	}

	public function getValue() : mixed{
		return $this->value;
	}
}

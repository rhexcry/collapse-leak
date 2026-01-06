<?php

declare(strict_types=1);

namespace collapse\punishments\rule;

use pocketmine\lang\Translatable;

final class Rule{

	public function __construct(
		private string  $code,
		private string  $description,
		private ?string $croppedDescription = null,
		private ?int    $duration = null,
		private array   $parameters = []
	){}

	public function getCode() : string{
		return $this->code;
	}

	public function getDescription() : string{
		return $this->description;
	}

	public function getCroppedDescription() : ?string{
		return $this->croppedDescription;
	}

	public function getDuration() : ?int{
		return $this->duration;
	}

	public function getTranslation(bool $cropped = false) : Translatable{
		return new Translatable(
			($cropped
				? ($this->croppedDescription ?? $this->description)
				: $this->description), $this->parameters
		);
	}
}

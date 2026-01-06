<?php

declare(strict_types=1);

namespace collapse\i18n\types;

readonly final class DynamicLanguage implements LanguageInterface{

	public function __construct(
		private string $code,
		private string $name
	){}

	public function getCode() : string{
		return $this->code;
	}

	public function getName() : string{
		return $this->name;
	}
}

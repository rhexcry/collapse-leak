<?php

declare(strict_types=1);

namespace collapse\i18n\types;

interface LanguageInterface{

	public function getCode() : string;

	public function getName() : string;
}

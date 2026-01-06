<?php

declare(strict_types=1);

namespace collapse\wallet\currency;

interface Currency{

	public function getName() : string;

	public function getDisplayName() : string;

	public function getDefaultValue() : int;

}

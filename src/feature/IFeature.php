<?php

declare(strict_types=1);

namespace collapse\feature;

interface IFeature{

	public function initialize(FeatureContext $context) : void;

	public function shutdown() : void;

}

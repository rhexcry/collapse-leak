<?php

declare(strict_types=1);

namespace collapse\mongo\operation;

use MongoDB\Client;

abstract class MongoOperation{

	public int $promiseId;

	abstract public function getType() : MongoOperationType;

	abstract public function work(Client $client) : mixed;
}

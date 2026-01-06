<?php

declare(strict_types=1);

namespace collapse\mongo\operation;

use MongoDB\Client;
use pocketmine\thread\NonThreadSafeValue;

final class FindOneOperation extends MongoOperation{

	public function __construct(
		private readonly string $database,
		private readonly string $collection,
		private readonly array $filter = [],
		private readonly array $options = []
	){}

	public function getType() : MongoOperationType{
		return MongoOperationType::FindOne;
	}

	public function work(Client $client) : NonThreadSafeValue{
		return new NonThreadSafeValue($client->selectCollection($this->database, $this->collection)->findOne(
			$this->filter,
			$this->options
		));
	}
}

<?php

declare(strict_types=1);

namespace collapse\mongo\operation;

use MongoDB\Client;
use pocketmine\thread\NonThreadSafeValue;
use function iterator_to_array;

final class FindOperation extends MongoOperation{

	public function __construct(
		private readonly string $database,
		private readonly string $collection,
		private readonly array $filter = [],
		private readonly array $options = []
	){}

	public function getType() : MongoOperationType{
		return MongoOperationType::Find;
	}

	public function work(Client $client) : NonThreadSafeValue{
		return new NonThreadSafeValue(iterator_to_array($client->selectCollection($this->database, $this->collection)->find(
			$this->filter,
			$this->options
		)));
	}
}

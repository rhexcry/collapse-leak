<?php

declare(strict_types=1);

namespace collapse\mongo\operation;

use MongoDB\Client;
use pocketmine\thread\NonThreadSafeValue;

final class ReplaceOneOperation extends MongoOperation{

	public function __construct(
		private readonly string $database,
		private readonly string $collection,
		private readonly array|object $filter,
		private readonly array|object $replacement,
		private readonly array $options = []
	){}

	public function getType() : MongoOperationType{
		return MongoOperationType::ReplaceOne;
	}

	public function work(Client $client) : NonThreadSafeValue{
		$client->selectCollection($this->database, $this->collection)->replaceOne(
			$this->filter,
			$this->replacement,
			$this->options
		);
		return new NonThreadSafeValue([]);
	}
}

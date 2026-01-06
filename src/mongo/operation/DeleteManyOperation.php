<?php

declare(strict_types=1);

namespace collapse\mongo\operation;

use MongoDB\Client;
use pocketmine\thread\NonThreadSafeValue;

final class DeleteManyOperation extends MongoOperation{

	public function __construct(
		private readonly string $database,
		private readonly string $collection,
		private readonly array $filter,
		private readonly array $options = []
	){}

	public function getType() : MongoOperationType{
		return MongoOperationType::DeleteMany;
	}

	public function work(Client $client) : NonThreadSafeValue{
		$client->selectCollection($this->database, $this->collection)->deleteMany(
			$this->filter,
			$this->options
		);
		return new NonThreadSafeValue([]);
	}
}
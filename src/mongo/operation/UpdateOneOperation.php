<?php

declare(strict_types=1);

namespace collapse\mongo\operation;

use MongoDB\Client;
use MongoDB\Model\BSONDocument;

final class UpdateOneOperation extends MongoOperation{

	public function __construct(
		private readonly string $database,
		private readonly string $collection,
		private readonly array $filter,
		private readonly BSONDocument|array $update,
		private readonly array $options = []
	){}

	public function getType() : MongoOperationType{
		return MongoOperationType::UpdateOne;
	}

	public function work(Client $client) : int{
		$result = $client->selectCollection($this->database, $this->collection)->updateOne(
			$this->filter,
			$this->update,
			$this->options
		);
		return $result->getModifiedCount() ?? $result->getUpsertedCount() ?? 0;
	}
}

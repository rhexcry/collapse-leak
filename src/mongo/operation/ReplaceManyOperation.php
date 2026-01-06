<?php

declare(strict_types=1);

namespace collapse\mongo\operation;

use MongoDB\Client;
use pocketmine\thread\NonThreadSafeValue;

final class ReplaceManyOperation extends MongoOperation{

	public function __construct(
		private readonly string $database,
		private readonly string $collection,
		private readonly array $documents,
		private readonly array $options = []
	){}

	public function getType() : MongoOperationType{
		return MongoOperationType::ReplaceMany;
	}

	public function work(Client $client) : NonThreadSafeValue{
		$collection = $client->selectCollection($this->database, $this->collection);
		foreach($this->documents as $document){
			$collection->replaceOne(['_id' => $document['_id']], $document, $this->options);
		}
		return new NonThreadSafeValue([]);
	}
}

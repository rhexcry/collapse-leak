<?php

declare(strict_types=1);

namespace collapse\leaderboard\mongo;

use collapse\mongo\operation\MongoOperation;
use collapse\mongo\operation\MongoOperationType;
use MongoDB\Client;
use pocketmine\thread\NonThreadSafeValue;

final class LeaderboardOperation extends MongoOperation{

	public function __construct(
		private readonly string $database,
		private readonly string $collection,
		private readonly string $sort,
		private readonly int $limit
	){}

	public function getType() : MongoOperationType{
		return MongoOperationType::Find;
	}

	public function work(Client $client) : NonThreadSafeValue{
		return new NonThreadSafeValue($client->selectCollection($this->database, $this->collection)->find(options: [
			'sort' => [$this->sort => -1],
			'limit' => $this->limit
		])->toArray());
	}
}

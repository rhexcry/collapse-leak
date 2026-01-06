<?php

declare(strict_types=1);

namespace collapse\game\duel\records;

use collapse\game\duel\types\DuelMode;
use collapse\game\duel\types\DuelType;
use collapse\mongo\MongoUtils;
use MongoDB\BSON\ObjectId;
use MongoDB\Model\BSONDocument;
use pocketmine\item\Item;
use pocketmine\nbt\LittleEndianNbtSerializer;
use function array_map;

class SavedDuelRecord extends DuelRecord{

	public static function fromBsonDocument(BSONDocument $document) : self{
		$document = MongoUtils::bsonDocumentToArray($document);
		return new self(
			$document['_id'],
			DuelMode::from($document['mode']),
			DuelType::from($document['type']),
			$document['winners'],
			$document['losers'],
			$document['time'],
			$document['duration'],
			array_map(fn(array $inventory) : array => array_map(function(BSONDocument $document) : Item{
				$item = MongoUtils::bsonDocumentToArray($document);
				return Item::nbtDeserialize(
					(new LittleEndianNbtSerializer())->read($item['nbt'])->mustGetCompoundTag()
				);
			}, $inventory), $document['inventories']),
			$document['statistics'],
			$document['potionEffects']
		);
	}

	public function __construct(
		private readonly ObjectId $id,
		DuelMode $mode,
		DuelType $type,
		array $winners,
		array $losers,
		int $time,
		int $duration,
		array $inventories,
		array $statistics,
		array $potionEffects
	){
		parent::__construct($mode, $type, $winners, $losers, $time, $duration, $inventories, $statistics, $potionEffects);
	}

	public function getId() : ObjectId{
		return $this->id;
	}
}

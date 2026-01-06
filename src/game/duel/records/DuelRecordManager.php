<?php

declare(strict_types=1);

namespace collapse\game\duel\records;

use collapse\game\duel\Duel;
use collapse\game\duel\DuelManager;
use collapse\mongo\MongoWrapper;
use collapse\mongo\operation\InsertOneOperation;
use collapse\player\profile\trait\PlayerProfileResolver;
use collapse\Practice;
use MongoDB\BSON\ObjectId;
use MongoDB\Collection;
use MongoDB\Model\BSONDocument;
use function array_map;
use function array_merge;
use function iterator_to_array;

final class DuelRecordManager{
	use PlayerProfileResolver;

	private const string COLLECTION = 'duel_matches';

	private Collection $collection;

	public function __construct(
		private readonly DuelManager $duelManager
	){
		$this->collection = MongoWrapper::getClient()->selectCollection(Practice::getDatabaseName(), self::COLLECTION);
	}

	public function getRecordById(ObjectId $id) : ?SavedDuelRecord{
		$document = $this->collection->findOne(['_id' => $id]);
		if($document instanceof BSONDocument){
			return SavedDuelRecord::fromBsonDocument($document);
		}
		return null;
	}

	/**
	 * @return SavedDuelRecord[]
	 */
	public function getRecordsByXuid(string $xuid) : array{
		return array_map(static function(BSONDocument $document) : SavedDuelRecord{
			return SavedDuelRecord::fromBsonDocument($document);
		}, iterator_to_array($this->collection->find(['$or' => [
			['winners' => ['$in' => [$xuid]]],
			['losers' => ['$in' => [$xuid]]]
		]])));
	}

	public function onMatchEnd(Duel $duel) : void{
		$record = $duel->getRecord();
		MongoWrapper::push(new InsertOneOperation(
			$this->collection->getDatabaseName(),
			$this->collection->getCollectionName(),
			$record->export()
		))->onResolve(function(?ObjectId $id) use ($record) : void{
			if($id === null){
				return;
			}
			foreach(array_merge($record->getWinners(), $record->getLosers()) as $xuid){
				$profile = self::resolveProfileByXuid(Practice::getPlayerByXuid($xuid) ?? $xuid);
				$profile->onDuelMatch($id, $record);

				if($record->hasWinner($xuid)){
					$profile->onDuelWin($record->getType(), $record->getMode());
				}else{
					$profile->onDuelLoss($record->getType(), $record->getMode());
				}

				if(!empty($record->getEloUpdates()) && ($eloUpdate = $record->getEloUpdates()[$xuid]['total'] ?? null) !== null){
					$profile->setDuelsElo($record->getMode(), $eloUpdate);
					$profile->onRecalculateGlobalElo();
				}

				$profile->save();
			}
		});
	}
}

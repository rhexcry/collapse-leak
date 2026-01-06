<?php

declare(strict_types=1);

namespace collapse\system\telegram;

use collapse\mongo\MongoWrapper;
use collapse\mongo\operation\InsertOneOperation;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use MongoDB\Collection;
use MongoDB\Model\BSONDocument;
use function time;
use function mt_rand;

final class TelegramManager{

	private const string COLLECTION = 'pending_links';

	private Collection $collection;

	public function __construct(private readonly Practice $plugin){
		$this->collection = MongoWrapper::getClient()->selectCollection(Practice::getDatabaseName(), self::COLLECTION);
		$this->collection->createIndex(['xuid' => 1], ['unique' => true]);
		$this->collection->createIndex(['code' => 1], ['unique' => true]);
		$this->collection->createIndex(['generated_at' => 1]);

		//$this->plugin->getServer()->getCommandMap()->register('collapse', new LinkCommand($this));
	}

	public function generateCode(CollapsePlayer $player) : ?string{
		$xuid = $player->getXuid();
		$existing = $this->getPendingLink($xuid);
		if($existing !== null){
			return null;
		}

		do{
			$code = $this->generateUniqueCode();
		}while($this->codeExists($code));

		$data = ['xuid' => $xuid, 'code' => $code, 'generated_at' => time()];
		MongoWrapper::push(new InsertOneOperation(
			$this->collection->getDatabaseName(),
			$this->collection->getCollectionName(),
			$data
		));

		return $code;
	}

	public function getCode(CollapsePlayer $player) : ?string{
		$existing = $this->getPendingLink($player->getXuid());
		return $existing !== null ? $existing['code'] : null;
	}

	private function generateUniqueCode() : string{
		$letters = range('A', 'Z');
		$digits = range(0, 9);
		$code = '';
		for ($i = 0; $i < 3; $i++) {
			$code .= $letters[mt_rand(0, 25)];
			$code .= $digits[mt_rand(0, 9)];
		}
		return $code;
	}

	private function codeExists(string $code) : bool{
		return $this->collection->countDocuments(['code' => $code]) > 0;
	}

	private function getPendingLink(string $xuid) : ?array{
		$document = $this->collection->findOne(['xuid' => $xuid]);
		return $document instanceof BSONDocument ? $document->getArrayCopy() : null;
	}
}
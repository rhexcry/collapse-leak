<?php

declare(strict_types=1);

namespace collapse\feature\concrete\quest;

use collapse\i18n\CollapseTranslationFactory;
use MongoDB\Model\BSONDocument;
use pocketmine\lang\Translatable;

final class QuestProgress{
	public function __construct(
		private readonly string $playerXuid,
		private readonly string $questId,
		private array           $data = [],
		private bool            $completed = false){
	}

	public static function fromBSON(BSONDocument $doc) : self{
		return new self(
			$doc['player_xuid'],
			$doc['quest_id'],
			$doc['data']->getArrayCopy(),
			$doc['completed']
		);
	}

	public function serialize() : array{
		return [
			'player_xuid' => $this->playerXuid,
			'quest_id' => $this->questId,
			'data' => $this->data,
			'completed' => $this->completed
		];
	}

	public function getPlayerXuid() : string{
		return $this->playerXuid;
	}

	public function getQuestId() : string{
		return $this->questId;
	}

	public function getData() : array{
		return $this->data;
	}

	public function isCompleted() : bool{
		return $this->completed;
	}

	public function setCompleted() : void{
		$this->completed = true;
	}

	public function set(string $key, mixed $value) : void{
		$this->data[$key] = $value;
	}

	/**
	 * @return Translatable[]
	 */
	public function toDisplay() : array{
		if($this->completed){
			return [CollapseTranslationFactory::quest_progress_display_completed()];
		}

		$huinya = [];
		if(isset($this->data['currentBreak'])){
			$huinya[] = CollapseTranslationFactory::quest_progress_display_broken((string) $this->data['currentBreak']);
		}

		if(isset($this->data['currentKills'])){
			$huinya[] = CollapseTranslationFactory::quest_progress_display_killed((string) $this->data['currentKills']);
		}

		if(isset($this->data['currentPlace'])){
			$huinya[] = CollapseTranslationFactory::quest_progress_display_placed((string) $this->data['currentPlace']);
		}

		return $huinya;
	}
}

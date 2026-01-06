<?php

declare(strict_types=1);

namespace collapse\feature\concrete\quest;

use collapse\feature\trigger\types\ITrigger;
use MongoDB\Model\BSONDocument;
use pocketmine\lang\Translatable;
use function array_map;
use function serialize;
use function unserialize;

final readonly class Quest{
	public function __construct(
		private string       $id,
		private Translatable $name,
		private Translatable $description,
		/** @var ITrigger[] */
		private array        $triggers = [],
		private string       $iconPath = ''){
	}

	public static function fromBSON(BSONDocument $doc) : self{
		$triggers = array_map(
			fn(BSONDocument $triggerData) => unserialize($triggerData['class']),
			$doc['triggers']->getArrayCopy()
		);

		return new self(
			$doc['id'],
			unserialize($doc['name']),
			unserialize($doc['description']),
			$triggers,
			$doc['iconPath']
		);
	}

	public function serialize() : array{
		return [
			'id' => $this->id,
			'name' => serialize($this->name),
			'description' => serialize($this->description),
			'triggers' => array_map(
				fn(ITrigger $trigger) => ['class' => serialize($trigger)],
				$this->triggers
			),
			'iconPath' => $this->iconPath
		];
	}

	public function getId() : string{
		return $this->id;
	}

	public function getName() : Translatable{
		return $this->name;
	}

	public function getDescription() : Translatable{
		return $this->description;
	}

	public function getTriggers() : array{
		return $this->triggers;
	}

	public function getIconPath() : string{
		return $this->iconPath;
	}
}

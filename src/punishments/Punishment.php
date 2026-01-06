<?php

declare(strict_types=1);

namespace collapse\punishments;

use collapse\mongo\MongoUtils;
use collapse\player\profile\Profile;
use MongoDB\Model\BSONDocument;
use pocketmine\command\CommandSender;
use function time;

final readonly class Punishment{

	public const string DATE_TIME_FORMAT = 'Y-m-d H:i';

	public static function fromBsonDocument(BSONDocument $document) : self{
		return new self(MongoUtils::bsonDocumentToArray($document));
	}

	public static function createFromProfile(PunishmentType $type, Profile $profile, string $reason, ?CommandSender $sender, ?int $expires) : self{
		return new self([
			'type' => $type->value,
			'xuid' => $profile->getXuid(),
			'device_id' => $profile->getDeviceId(),
			'ip' => $profile->getIp(),
			'playerName' => $profile->getPlayerName(),
			'lowerCasePlayerName' => $profile->getLowerCasePlayerName(),
			'reason' => $reason,
			'sender' => $sender->getName(),
			'createdAt' => time(),
			'expiresAt' => $expires
		]);
	}

	/**
	 * @param array{
	 *     type: string,
	 *     xuid: string,
	 *     device_id: string,
	 *     ip: string,
	 *     playerName: string,
	 *     lowerCasePlayerName: string,
	 *     reason: string,
	 *     sender: string|null,
	 *     createdAt: int,
	 *     expiresAt: int|null
	 * } $data
	 */
	public function __construct(
		private array $data
	){}

	public function getType() : PunishmentType{
		return PunishmentType::from($this->data['type']);
	}

	public function getXuid() : string{
		return $this->data['xuid'];
	}

	public function getDeviceId() : string{
		return $this->data['device_id'];
	}

	public function getIp() : string{
		return $this->data['ip'];
	}

	public function getPlayerName() : string{
		return $this->data['playerName'];
	}

	public function getLowerCasePlayerName() : string{
		return $this->data['lowerCasePlayerName'];
	}

	public function getReason() : string{
		return $this->data['reason'];
	}

	public function getSender() : ?string{
		return $this->data['sender'] ?? null;
	}

	public function getCreation() : int{
		return $this->data['createdAt'];
	}

	public function getExpiration() : ?int{
		return $this->data['expiresAt'] ?? null;
	}

	public function isActive() : bool{
		return $this->getExpiration() === null || $this->getExpiration() > time();
	}

	public function export() : array{
		return $this->data;
	}
}

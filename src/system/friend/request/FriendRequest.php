<?php

declare(strict_types=1);

namespace collapse\system\friend\request;

use MongoDB\BSON\ObjectId;
use MongoDB\Model\BSONDocument;

final class FriendRequest{

	public function __construct(
		private readonly ObjectId $id,
		private readonly string $senderId,
		private readonly string $receiverId,
		private readonly ?string $message,
		private readonly int $createdAt,
		private FriendRequestStatus $status
	){}

	public static function fromBSONDocument(BSONDocument $document) : self{
		return new self(
			$document['_id'],
			$document['sender_id'],
			$document['receiver_id'],
			$document['message'] ?? null,
			$document['created_at'],
			FriendRequestStatus::from($document['status'])
		);
	}

	public function toArray() : array{
		return [
			'id' => $this->id,
			'sender_id' => $this->senderId,
			'receiver_id' => $this->receiverId,
			'message' => $this->message,
			'created_at' => $this->createdAt,
			'status' => $this->status->value
		];
	}

	public function getId() : ObjectId{
		return $this->id;
	}

	public function getSenderXuid() : string{
		return $this->senderId;
	}

	public function getReceiverXuid() : string{
		return $this->receiverId;
	}

	public function getMessage() : ?string{
		return $this->message;
	}

	public function getCreatedAt() : int{
		return $this->createdAt;
	}

	public function getStatus() : FriendRequestStatus{
		return $this->status;
	}

	public function setStatus(FriendRequestStatus $status) : void{
		$this->status = $status;
	}
}

<?php

declare(strict_types=1);

namespace collapse\system\friend;

use collapse\i18n\CollapseTranslationFactory;
use collapse\mongo\MongoWrapper;
use collapse\mongo\operation\InsertOneOperation;
use collapse\player\CollapsePlayer;
use collapse\player\profile\Profile;
use collapse\Practice;
use collapse\system\friend\command\FriendsCommand;
use collapse\system\friend\request\FriendRequest;
use collapse\system\friend\request\FriendRequestStatus;
use MongoDB\Collection;
use MongoDB\Model\BSONDocument;
use function array_map;
use function time;

final class FriendManager{

	private const string COLLECTION = 'friends';
	private const string REQUESTS_COLLECTION = 'friend_requests';
	private const int REQUEST_EXPIRE_SECONDS = 86400;
	private const int MAX_FRIENDS = 30;

	private Collection $friendsCollection;
	private Collection $requestsCollection;
	private \PrefixedLogger $logger;

	public function __construct(private readonly Practice $plugin){
		$this->friendsCollection = MongoWrapper::getClient()->selectCollection(Practice::getDatabaseName(), self::COLLECTION);
		$this->requestsCollection = MongoWrapper::getClient()->selectCollection(Practice::getDatabaseName(), self::REQUESTS_COLLECTION);

		$this->friendsCollection->createIndex(['player_id' => 1]);
		$this->friendsCollection->createIndex(['friend_id' => 1]);
		$this->requestsCollection->createIndex(['sender_id' => 1]);
		$this->requestsCollection->createIndex(['receiver_id' => 1]);
		$this->requestsCollection->createIndex(['created_at' => 1]);

		$this->logger = new \PrefixedLogger($this->plugin->getLogger(), 'Friends');

		$this->plugin->getServer()->getCommandMap()->register('collapse', new FriendsCommand());
	}

	public function sendFriendRequest(CollapsePlayer $sender, Profile $receiver, string $message = '') : void{
		if($this->isFriend($sender->getProfile(), $receiver)){
			$sender->sendTranslatedMessage(CollapseTranslationFactory::friends_player_already_added($receiver->getPlayerName()));
			return;
		}

		if($this->hasPendingRequest($sender->getProfile(), $receiver)){
			$sender->sendTranslatedMessage(CollapseTranslationFactory::friends_request_already_sent($receiver->getPlayerName()));
			return;
		}

		$requestData = [
			'sender_id' => $sender->getXuid(),
			'receiver_id' => $receiver->getXuid(),
			'message' => $message,
			'created_at' => time(),
			'status' => FriendRequestStatus::Pending->value
		];

		MongoWrapper::push(new InsertOneOperation(
			$this->requestsCollection->getDatabaseName(),
			$this->requestsCollection->getCollectionName(),
			$requestData
		))->onResolve(
			function() use ($sender, $receiver) : void{
				$sender->sendTranslatedMessage(CollapseTranslationFactory::friends_request_successfully_sent($receiver->getPlayerName()));
				if(($player = $receiver->getPlayer()) instanceof CollapsePlayer){
					$player->sendTranslatedMessage(CollapseTranslationFactory::friends_request_new_incoming($sender->getName()));
				}
			});
	}

	public function cancelFriendRequest(Profile $profile, FriendRequest $request) : void{
		$this->requestsCollection->deleteOne([
			'_id' => $request->getId(),
			'sender_id' => $profile->getXuid()
		]);

		if(($player = $profile->getPlayer()) instanceof CollapsePlayer){
			$player->sendTranslatedMessage(CollapseTranslationFactory::friend_request_cancelled());
		}
	}

	public function acceptFriendRequest(CollapsePlayer $player, FriendRequest $request) : void{
		if($this->getFriendCount($player->getProfile()) >= self::MAX_FRIENDS){
			$player->sendTranslatedMessage(CollapseTranslationFactory::friend_request_max_friends_exceeded());
			return;
		}

		$senderProfile = $this->plugin->getProfileManager()->getProfileByXuid($request->getSenderXuid());

		if($this->getFriendCount($senderProfile) >= self::MAX_FRIENDS){
			$player->sendTranslatedMessage(CollapseTranslationFactory::friend_request_user_max_friends_exceeded($senderProfile->getPlayerName()));
			return;
		}

		$this->addFriend($senderProfile, $player->getProfile());
		$this->addFriend($player->getProfile(), $senderProfile);

		$player->sendTranslatedMessage(CollapseTranslationFactory::friend_request_accepted($senderProfile->getPlayerName()));

		if(($sender = $senderProfile->getPlayer()) instanceof CollapsePlayer){
			$sender->sendTranslatedMessage(CollapseTranslationFactory::friend_request_accepted_to($player->getName()));
		}

		$this->requestsCollection->deleteOne(['_id' => $request->getId()]);
	}

	public function getFriendRequest(Profile $sender, Profile $receiver, FriendRequestStatus $status) : ?FriendRequest{
		$filter = [
			'sender_id' => $sender->getXuid(),
			'receiver_id' => $receiver->getXuid(),
			'status' => $status->value
		];

		$document = $this->requestsCollection->findOne($filter);

		return $document instanceof BSONDocument ? FriendRequest::fromBSONDocument($document) : null;
	}

	public function getFriendCount(Profile $profile) : int{
		$filter = [
			'$or' => [
				[
					'sender' => $profile->getLowerCasePlayerName(),
					'status' => FriendRequestStatus::Accepted->value
				],
				[
					'receiver' => $profile->getLowerCasePlayerName(),
					'status' => FriendRequestStatus::Accepted->value
				]
			]
		];
		return $this->friendsCollection->countDocuments($filter);
	}

	public function rejectFriendRequest(Profile $receiver, FriendRequest $request) : void{
		$this->requestsCollection->deleteOne([
			'_id' => $request->getId(),
			'receiver_id' => $receiver->getXuid()
		]);

		if(($player = $receiver->getPlayer()) instanceof CollapsePlayer){
			$player->sendTranslatedMessage(CollapseTranslationFactory::friend_request_declined(Practice::getInstance()->getProfileManager()->getProfileByXuid($request->getSenderXuid())->getPlayerName()));
		}
	}

	private function addFriend(Profile $profile1, Profile $profile2) : void{
		$friendData = [
			'player_id' => $profile1->getXuid(),
			'friend_id' => $profile2->getXuid(),
			'since' => time()
		];

		MongoWrapper::push(new InsertOneOperation(
			$this->friendsCollection->getDatabaseName(),
			$this->friendsCollection->getCollectionName(),
			$friendData
		));
	}

	public function removeFriend(Profile $profile1, Profile $profile2) : void{
		$this->friendsCollection->deleteMany([
			'$or' => [
				['player_id' => $profile1->getXuid(), 'friend_id' => $profile2->getXuid()],
				['player_id' => $profile2->getXuid(), 'friend_id' => $profile1->getXuid()],
			]
		]);

		if(($player1 = $profile1->getPlayer()) instanceof CollapsePlayer){
			$player1->sendTranslatedMessage(CollapseTranslationFactory::friend_deleted($profile2->getPlayerName()));
		}

		if(($player2 = $profile2->getPlayer()) instanceof CollapsePlayer){
			$player2->sendTranslatedMessage(CollapseTranslationFactory::friend_deleted($profile1->getPlayerName()));
		}
	}

	public function getFriends(Profile $profile) : array{
		$cursor = $this->friendsCollection->find([
			'player_id' => $profile->getXuid()
		]);

		return array_map(fn(BSONDocument $document) => new Friend($document['friend_id'], $document['since']), $cursor->toArray());
	}

	/**
	 * @return FriendRequest[]
	 */
	public function getOutgoingRequests(Profile $profile) : array{
		$this->cleanExpiredRequests();

		$cursor = $this->requestsCollection->find([
			'sender_id' => $profile->getXuid(),
			'status' => FriendRequestStatus::Pending->value
		]);

		return array_map(
			fn(BSONDocument $doc) => FriendRequest::fromBSONDocument($doc),
			$cursor->toArray()
		);
	}

	/**
	 * @return FriendRequest[]
	 */
	public function getIncomingRequests(Profile $profile) : array{
		$this->cleanExpiredRequests();

		$cursor = $this->requestsCollection->find([
			'receiver_id' => $profile->getXuid(),
			'status' => FriendRequestStatus::Pending->value
		]);

		return array_map(
			fn(BSONDocument $doc) => FriendRequest::fromBSONDocument($doc),
			$cursor->toArray()
		);
	}

	public function isFriend(Profile $profile1, Profile $profile2) : bool{
		return $this->friendsCollection->countDocuments([
			'player_id' => $profile1->getXuid(),
			'friend_id' => $profile2->getXuid()
		]) > 0;
	}

	public function hasPendingRequest(Profile $profile1, Profile $profile2) : bool{
		return $this->requestsCollection->countDocuments([
			'sender_id' => $profile1->getXuid(),
			'receiver_id' => $profile2->getXuid(),
			'status' => FriendRequestStatus::Pending->value
		]) > 0;
	}

	private function cleanExpiredRequests() : void{
		$this->requestsCollection->deleteMany([
			'created_at' => ['$lt' => time() - self::REQUEST_EXPIRE_SECONDS],
			'status' => FriendRequestStatus::Pending->value
		]);
	}
}

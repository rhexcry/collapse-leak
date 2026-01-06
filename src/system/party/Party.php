<?php

declare(strict_types=1);

namespace collapse\system\party;

use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\system\party\event\PartyDisbandEvent;
use collapse\system\party\event\PartyPlayerJoinedEvent;
use collapse\system\party\event\PartyPlayerLeftEvent;
use pocketmine\lang\Translatable;
use pocketmine\utils\TextFormat;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use WeakMap;

final class Party{

	private UuidInterface $id;
	private CollapsePlayer $leader;
	private array $members = [];
	private array $invitedPlayers = [];
	private int $maxSize;

	private WeakMap $memberMap;

	public function __construct(CollapsePlayer $leader, int $maxSize = 2){
		$this->id = Uuid::uuid4();
		$this->leader = $leader;
		$this->maxSize = $maxSize;
		$this->memberMap = new WeakMap();

		$this->addMember($leader);
	}

	public function getId() : UuidInterface{
		return $this->id;
	}

	public function getLeader() : CollapsePlayer{
		return $this->leader;
	}

	public function getMembers() : array{
		return array_values($this->members);
	}

	public function getInvitedPlayers() : array{
		return array_values($this->invitedPlayers);
	}

	public function addMember(CollapsePlayer $player) : void{
		if($this->getSize() >= $this->maxSize){
			return;
		}

		$ev = new PartyPlayerJoinedEvent($this, $player);
		$ev->call();

		$this->members[$player->getXuid()] = $player;
		$this->memberMap[$player] = true;

		$partyManager = Practice::getInstance()->getPartyManager();
		$partyManager->addPlayerToParty($player, $this);

		$this->broadcastMessage(CollapseTranslationFactory::party_member_join($player->getNameWithRankColor(), (string) $this->getSize(), (string) $this->getMaxSize()));

		unset($this->invitedPlayers[$player->getXuid()]);
	}

	public function removeMember(CollapsePlayer $player) : void{
		$playerId = $player->getXuid();

		if(isset($this->members[$playerId])){
			unset($this->members[$playerId]);
			unset($this->memberMap[$player]);

			$partyManager = Practice::getInstance()->getPartyManager();
			$partyManager->removePlayerFromParty($player);

			$ev = new PartyPlayerLeftEvent($this, $player);
			$ev->call();
		}
	}

	public function invitePlayer(CollapsePlayer $player) : void{
		$playerId = $player->getXuid();

		if($this->isInvited($player)){
			return;
		}

		$this->invitedPlayers[$playerId] = $player;
		$this->broadcastMessage(CollapseTranslationFactory::party_member_invite_broadcast($player->getNameWithRankColor()));
		$player->sendTranslatedMessage(CollapseTranslationFactory::party_member_invite($this->leader->getNameWithRankColor()));
	}

	public function revokeInvite(CollapsePlayer $player) : void{
		$playerId = $player->getXuid();

		if(isset($this->invitedPlayers[$playerId])){
			unset($this->invitedPlayers[$playerId]);

			$player->sendMessage(
				TextFormat::RED . "Your party invitation has been revoked."
			);
		}
	}

	public function disband() : void{
		$ev = new PartyDisbandEvent($this);
		$ev->call();

		foreach($this->members as $member){
			unset($this->memberMap[$member]);
		}

		$this->members = [];
		$this->invitedPlayers = [];
	}

	public function broadcastMessage(Translatable|string $message) : void{
		foreach($this->members as $member){
			if($member->isOnline()){
				if($message instanceof Translatable){
					$message = $member->getProfile()->getTranslator()->translate($message);
				}

				$member->sendMessage(TextFormat::AQUA . "[Party] " . TextFormat::RESET . $message);
			}
		}
	}

	public function isMember(CollapsePlayer $player) : bool{
		return isset($this->memberMap[$player]);
	}

	public function isInvited(CollapsePlayer $player) : bool{
		return isset($this->invitedPlayers[$player->getXuid()]);
	}

	public function getSize() : int{
		return count($this->members);
	}

	public function getMaxSize() : int{
		return $this->maxSize;
	}

	public function __destruct(){
		$this->members = [];
		$this->invitedPlayers = [];
	}
}
<?php

declare(strict_types=1);

namespace collapse\game\duel\requests;

use collapse\game\duel\Duel;
use collapse\game\duel\DuelManager;
use collapse\game\duel\types\DuelMode;
use collapse\game\duel\types\DuelType;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use pocketmine\world\sound\PopSound;
use function array_filter;
use function array_values;
use const ARRAY_FILTER_USE_BOTH;

final class DuelRequestManager{

	public const int EXPIRES_TIME = 30;

	/** @var DuelRequest[][] */
	private array $requests = [];

	public function __construct(
		private readonly DuelManager $duelManager
	){}

	public function send(CollapsePlayer $player, CollapsePlayer $sender, DuelMode $mode) : void{
		if(isset($this->requests[$player->getXuid()][$sender->getXuid()])){
			$request = $this->requests[$player->getXuid()][$sender->getXuid()];
			if(!$request->isExpired() && $request->getMode() !== $mode){
				$sender->sendTranslatedMessage(CollapseTranslationFactory::duels_requests_already_sent());
				return;
			}
		}

		$this->requests[$player->getXuid()][$sender->getXuid()] = new DuelRequest(
			$player->getXuid(),
			$sender->getXuid(),
			$mode
		);
		$sender->sendTranslatedMessage(CollapseTranslationFactory::duels_requests_successfully(
			$mode->toDisplayName(),
			$player->getNameWithRankColor()
		));
		$player->sendTranslatedMessage(CollapseTranslationFactory::duels_requests_receive(
			$mode->toDisplayName(),
			$sender->getNameWithRankColor()
		), false);
		$player->getWorld()->addSound($player->getLocation(), new PopSound(), [$player]);
	}

	public function accept(DuelRequest $request) : void{
		$player = Practice::getPlayerByXuid($request->getPlayerXuid());
		$sender = Practice::getPlayerByXuid($request->getSenderXuid());
		if(!($sender instanceof CollapsePlayer && $player instanceof CollapsePlayer)){
			return;
		}

		$kitEditorManager = $this->duelManager->getPlugin()->getKitEditorManager();
		$partyManager = $this->duelManager->getPlugin()->getPartyManager();
		if(($partyManager->isInParty($player) && $partyManager->isInParty($sender)) && (($playerParty = $partyManager->getPlayerParty($player))->getSize() === ($senderParty = $partyManager->getPlayerParty($sender))->getSize())){
			$this->duelManager->add(
				$this->duelManager->getMapPool()->getRandom($request->getMode()),
				DuelType::PartyRequest
			)->onCreate(function(Duel $duel) use ($kitEditorManager, $player, $sender, $playerParty, $senderParty) : void{
				if($playerParty->getSize() !== $senderParty->getSize()){
					return;
				}

				$allPlayerMembers = array_values($playerParty->getMembers());
				$validPlayerMembers = array_filter($allPlayerMembers, function(CollapsePlayer $member) use ($kitEditorManager): bool{
					if($member->isInGame() || $kitEditorManager->isEditing($member)){
						return false;
					}
					return true;
				});
				$invalidPlayerMembers = array_filter($allPlayerMembers, function(CollapsePlayer $member) use ($kitEditorManager): bool{
					return $member->isInGame() || $kitEditorManager->isEditing($member);
				});

				$allSenderMembers = array_values($senderParty->getMembers());
				$validSenderMembers = array_filter($allSenderMembers, function(CollapsePlayer $member) use ($kitEditorManager): bool{
					if($member->isInGame() || $kitEditorManager->isEditing($member)){
						return false;
					}
					return true;
				});
				$invalidSenderMembers = array_filter($allSenderMembers, function(CollapsePlayer $member) use ($kitEditorManager): bool{
					return $member->isInGame() || $kitEditorManager->isEditing($member);
				});

				if(count($validPlayerMembers) !== $playerParty->getSize() || count($validSenderMembers) !== $senderParty->getSize()){
					$allInvalidMembers = array_merge($invalidPlayerMembers, $invalidSenderMembers);
					$invalidNames = implode(', ', array_map(function(CollapsePlayer $member) : string{
						return $member->getNameWithRankColor();
					}, $allInvalidMembers));

					$allValidMembers = array_merge($validPlayerMembers, $validSenderMembers);
					/** @var CollapsePlayer $member */
					foreach($allValidMembers as $member){
						$member->sendTranslatedMessage(CollapseTranslationFactory::duels_requests_members_not_in_lobby($invalidNames));
					}
					$this->duelManager->closeDuel($duel);
					return;
				}

				foreach($validPlayerMembers as $member){
					$this->duelManager->getPlugin()->getLobbyManager()->removeFromLobby($member);
					$duel->getPlayerManager()->addPlayer($member);
				}
				foreach($validSenderMembers as $member){
					$this->duelManager->getPlugin()->getLobbyManager()->removeFromLobby($member);
					$duel->getPlayerManager()->addPlayer($member);
				}
			});
		}else{
			$invalid = array_filter([$player, $sender], fn($target) => $target->isInGame()  || $kitEditorManager->isEditing($target));
			if(!empty($invalid)){
				$names = implode(', ', array_map(fn($target) => $target->getNameWithRankColor(), $invalid));
				foreach([$player, $sender] as $target){
					$target->sendTranslatedMessage(CollapseTranslationFactory::duels_requests_players_not_in_lobby($names));
				}
				return;
			}

			$this->duelManager->add(
				$this->duelManager->getMapPool()->getRandom($request->getMode()),
				DuelType::Request
			)->onCreate(function(Duel $duel) use ($player, $sender) : void{
				foreach([$player, $sender] as $member){
					$this->duelManager->getPlugin()->getLobbyManager()->removeFromLobby($member);
					$duel->getPlayerManager()->addPlayer($member);
				}
			});
		}
	}

	/**
	 * @return DuelRequest[]
	 */
	public function getRequests(CollapsePlayer $player) : array{
		return array_filter($this->requests[$player->getXuid()] ?? [], function(DuelRequest $request, string $index) use ($player) : bool{
			if($request->isExpired()){
				unset($this->requests[$player->getXuid()][$index]);
				return false;
			}
			if($player->getServer()->getPlayerByXuid($request->getSenderXuid()) === null){
				return false;
			}
			return true;
		}, ARRAY_FILTER_USE_BOTH);
	}

	public function removeLastRequest(CollapsePlayer $player) : ?DuelRequest{
		$playerXuid = $player->getXuid();

		if(!isset($this->requests[$playerXuid]) || empty($this->requests[$playerXuid])){
			return null;
		}

		$activeRequests = $this->getRequests($player);

		if(empty($activeRequests)){
			return null;
		}

		$lastRequest = end($activeRequests);
		reset($activeRequests);

		$senderXuid = $lastRequest->getSenderXuid();
		if(isset($this->requests[$playerXuid][$senderXuid])){
			$removedRequest = $this->requests[$playerXuid][$senderXuid];
			unset($this->requests[$playerXuid][$senderXuid]);
			return $removedRequest;
		}

		return null;
	}

	public function getLastRequest(CollapsePlayer $player) : ?DuelRequest{
		$playerXuid = $player->getXuid();

		if(!isset($this->requests[$playerXuid]) || empty($this->requests[$playerXuid])){
			return null;
		}

		$activeRequests = $this->getRequests($player);

		if(empty($activeRequests)){
			return null;
		}

		return end($activeRequests);
	}
}

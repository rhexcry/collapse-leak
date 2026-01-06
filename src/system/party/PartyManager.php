<?php

declare(strict_types=1);

namespace collapse\system\party;

use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\system\party\command\PartyCommand;
use collapse\system\party\event\PartyCreateEvent;
use Ramsey\Uuid\UuidInterface;
use WeakMap;

final class PartyManager{

	/** @var array<string, Party> */
	private array $parties = [];

	private WeakMap $playerPartyMap;

	public function __construct(){
		$this->playerPartyMap = new WeakMap();
		Practice::getInstance()->getServer()->getCommandMap()->register('collapse', new PartyCommand());
		Practice::getInstance()->getServer()->getPluginManager()->registerEvents(new PartyListener(), Practice::getInstance());
	}

	public function createParty(CollapsePlayer $leader) : Party{
		if($this->isInParty($leader)){
			throw new \RuntimeException('Player is already in a party');
		}

		$party = new Party($leader);
		$this->parties[$party->getId()->toString()] = $party;
		$this->playerPartyMap[$leader] = $party;

		(new PartyCreateEvent($party))->call();

		return $party;
	}

	public function getParty(CollapsePlayer $player) : ?Party{
		return $this->playerPartyMap[$player] ?? null;
	}

	public function getPartyById(UuidInterface $id) : ?Party{
		return $this->parties[$id->toString()] ?? null;
	}

	public function disbandParty(Party $party) : void{
		$partyId = $party->getId()->toString();

		if(isset($this->parties[$partyId])){
			foreach($party->getMembers() as $member){
				unset($this->playerPartyMap[$member]);
			}

			$party->disband();

			unset($this->parties[$partyId]);
		}
	}

	public function addPlayerToParty(CollapsePlayer $player, Party $party) : void{
		$this->playerPartyMap[$player] = $party;
	}

	public function removePlayerFromParty(CollapsePlayer $player) : void{
		unset($this->playerPartyMap[$player]);
	}

	public function getPlayerParty(CollapsePlayer $player) : ?Party{
		return $this->getParty($player);
	}

	public function isInParty(CollapsePlayer $player) : bool{
		return isset($this->playerPartyMap[$player]);
	}

	public function cleanup() : void{
		$this->parties = array_filter($this->parties, function(Party $party){
			if($party->getSize() === 0){
				$this->disbandParty($party);
				return false;
			}

			return true;
		});
	}

	public function getAllParties() : array{
		return $this->parties;
	}
}
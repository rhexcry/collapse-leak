<?php

declare(strict_types=1);

namespace collapse\system\anticheat\check;

use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\system\anticheat\AnticheatSession;
use collapse\system\anticheat\event\AnticheatKickEvent;
use pocketmine\event\Event;
use pocketmine\network\mcpe\protocol\DataPacket;
use function array_filter;

abstract class Check{

	abstract public function getName() : string;
	abstract public function getSubType() : string;

	public function check(DataPacket $packet, AnticheatSession $session) : void{}
	public function checkEvent(Event $event, AnticheatSession $session) : void{}
	public function checkJustEvent(Event $event) : void{}

	public function failed(AnticheatSession $session) : void{
		$player = $session->getPlayer();
		if($player === null){
			return;
		}

		$session->addViolation($this);

		$reachedMaxViolations = $session->getViolationsByCheck($this) > $this->getMaxViolations();
		if($reachedMaxViolations){
			$session->addFinalViolation($this);
		}

		$reachedMaxFinalViolations = $session->getFinalViolationsByCheck($this) > $this->getMaxFinalViolations();
		if($reachedMaxFinalViolations && $reachedMaxViolations){
			$ev = new AnticheatKickEvent($session);
			$ev->call();

			if($ev->isCancelled()){
				return;
			}

			$player->kick('Unfair Advantage', 'Unfair Advantage', 'Unfair Advantage');
		}
	}

	public function debug(AnticheatSession $session, string $message) : void{
		$staffPlayers = array_filter(Practice::onlinePlayers(), function(CollapsePlayer $player) : bool{
			if($player->isConnected() && $player->getProfile()?->getRank()?->isStaffRank()){
				return true;
			}

			return false;
		});

		if($session->getPlayer() === null){
			return;
		}

		foreach($staffPlayers as $player){
			$player->sendTranslatedMessage(CollapseTranslationFactory::anticheat_staff_violation_message($session->getPlayer()->getName(), $this->getName() . $this->getSubType(), $message));
		}
	}

	abstract public function getMaxViolations() : int;
	abstract public function getMaxFinalViolations() : int;
}
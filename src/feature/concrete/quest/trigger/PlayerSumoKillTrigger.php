<?php

declare(strict_types=1);

namespace collapse\feature\concrete\quest\trigger;

use collapse\feature\concrete\quest\QuestFeature;
use collapse\feature\trigger\types\EventTrigger;
use collapse\game\event\PlayerKillPlayerGameEvent;
use collapse\Practice;

final class PlayerSumoKillTrigger extends EventTrigger{

	public function execute(object $event) : void{
		/** @var PlayerKillPlayerGameEvent $event */
		$player = $event->getKiller();
		$game = $player->getGame();
		if($game == null){
			return;
		}

		$mode = $game->getConfig()->getMode();
		$questManager = Practice::getInstance()->getFeatureManager()->get(QuestFeature::class)->getQuestManager();
		$progress = $questManager->getPlayerProgress($player->getProfile(), 'ffa_sumo_kills');

		if($progress->isCompleted()){
			return;
		}

		$progressData = $progress->getData();
		$currentKills = $progressData['currentKills'] ?? 0;

		$context = [
			'mode' => $mode,
			'currentKills' => $currentKills,
		];

		$conditionsMet = true;
		foreach($this->getConditions() as $condition){
			if(!$condition->isMet($player, $context)){
				$conditionsMet = false;
				break;
			}
		}

		if($conditionsMet){
			$progress->setCompleted();
			$this->executeActionsFor($player);
		}else{
			$progress->set('currentKills', $currentKills + 1);
		}

		$questManager->updateProgress($progress);
	}

	public function shouldHandle(object $event) : bool{
		return $event instanceof PlayerKillPlayerGameEvent;
	}

	public function getHandleableEvents() : array{
		return [PlayerKillPlayerGameEvent::class];
	}
}

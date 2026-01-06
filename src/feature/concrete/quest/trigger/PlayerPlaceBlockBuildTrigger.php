<?php

declare(strict_types=1);

namespace collapse\feature\concrete\quest\trigger;

use collapse\feature\concrete\quest\QuestFeature;
use collapse\feature\trigger\types\EventTrigger;
use collapse\game\event\BlockPlaceGameEvent;
use collapse\Practice;
use function in_array;

final class PlayerPlaceBlockBuildTrigger extends EventTrigger{

	public function execute(object $event) : void{
		/** @var BlockPlaceGameEvent $event */
		$player = $event->getPlayer();
		$game = $event->getGame();

		$questManager = Practice::getInstance()->getFeatureManager()->get(QuestFeature::class)->getQuestManager();
		$progress = $questManager->getPlayerProgress($player->getProfile(), 'build_place_blocks');

		if($progress->isCompleted()){
			return;
		}

		$progressData = $progress->getData();
		$currentBlocks = $progressData['currentPlace'] ?? 0;

		$context = [
			'currentPlace' => $currentBlocks,
			'block' => -1,
			'mode' => $game->getConfig()->getMode()
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
			$progress->set('currentPlace', $currentBlocks + 1);
		}

		$questManager->updateProgress($progress);
	}

	public function shouldHandle(object $event) : bool{
		return in_array($event::class, $this->getHandleableEvents(), true);
	}

	public function getHandleableEvents() : array{
		return [BlockPlaceGameEvent::class];
	}
}

<?php

declare(strict_types=1);

namespace collapse\feature\concrete\quest\action;

use collapse\feature\action\IAction;
use collapse\feature\concrete\quest\QuestFeature;
use collapse\feature\reward\concrete\ValuteReward;
use collapse\player\CollapsePlayer;
use collapse\Practice;

final class PlayerJoinAction implements IAction{

	public function execute(CollapsePlayer $player) : void{
		$questManager = Practice::getInstance()->getFeatureManager()->get(QuestFeature::class)->getQuestManager();
		$progress = $questManager->getPlayerProgress($player->getProfile(), 'first_join');
		$questManager->onQuestCompleted($progress);
		(new ValuteReward(100))->apply($player);
	}
}

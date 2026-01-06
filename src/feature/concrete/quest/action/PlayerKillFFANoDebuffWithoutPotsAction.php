<?php

declare(strict_types=1);

namespace collapse\feature\concrete\quest\action;

use collapse\feature\action\IAction;
use collapse\feature\concrete\quest\QuestFeature;
use collapse\feature\reward\concrete\ValuteReward;
use collapse\player\CollapsePlayer;
use collapse\Practice;

final class PlayerKillFFANoDebuffWithoutPotsAction implements IAction{

	public function execute(CollapsePlayer $player) : void{
		$questManager = Practice::getInstance()->getFeatureManager()->get(QuestFeature::class)->getQuestManager();
		$progress = $questManager->getPlayerProgress($player->getProfile(), 'ffa_nodebuff_kills_without_pots');
		$questManager->onQuestCompleted($progress);
		(new ValuteReward(10))->apply($player);
	}
}

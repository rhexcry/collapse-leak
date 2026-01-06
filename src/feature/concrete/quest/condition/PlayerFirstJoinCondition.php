<?php

declare(strict_types=1);

namespace collapse\feature\concrete\quest\condition;

use collapse\feature\concrete\quest\QuestFeature;
use collapse\feature\condition\ICondition;
use collapse\player\CollapsePlayer;
use collapse\Practice;

final class PlayerFirstJoinCondition implements ICondition{

	public function isMet(CollapsePlayer $player, mixed $data) : bool{
		$questManager = Practice::getInstance()->getFeatureManager()->get(QuestFeature::class)->getQuestManager();
		$progress = $questManager->getPlayerProgress($player->getProfile(), 'first_join');
		return !$progress->isCompleted();
	}
}

<?php

declare(strict_types=1);

namespace collapse\feature\concrete\quest;

use collapse\feature\concrete\quest\action\PlayerJoinAction;
use collapse\feature\concrete\quest\action\PlayerKillFFANoDebuffWithoutPotsAction;
use collapse\feature\concrete\quest\action\PlayerSumoKillAction;
use collapse\feature\concrete\quest\condition\FreeForAllGamemodeCondition;
use collapse\feature\concrete\quest\condition\ItemUsageCondition;
use collapse\feature\concrete\quest\condition\PlayerFirstJoinCondition;
use collapse\feature\concrete\quest\condition\PlayerKillCondition;
use collapse\feature\concrete\quest\condition\PlayerPlaceBlockCondition;
use collapse\feature\concrete\quest\trigger\PlayerJoinTrigger;
use collapse\feature\concrete\quest\trigger\PlayerKillFFANoDebuffWithoutPotsTrigger;
use collapse\feature\concrete\quest\trigger\PlayerPlaceBlockBuildTrigger;
use collapse\feature\concrete\quest\trigger\PlayerSumoKillTrigger;
use collapse\feature\condition\ConditionComposite;
use collapse\feature\condition\ConditionLogicType;
use collapse\feature\FeatureContext;
use collapse\feature\IFeature;
use collapse\feature\TriggerableFeature;
use collapse\game\ffa\types\FreeForAllMode;
use collapse\i18n\CollapseTranslationFactory;
use pocketmine\item\VanillaItems;
use function array_merge;

final class QuestFeature implements IFeature, TriggerableFeature{

	private QuestManager $questManager;

	public function __construct(){
	}

	public function initialize(FeatureContext $context) : void{
		$this->questManager = new QuestManager();
		$this->questManager->registerQuest(new Quest(
			'first_join',
			CollapseTranslationFactory::quest_concrete_first_join_name(),
			CollapseTranslationFactory::quest_concrete_first_join_description(),
			[new PlayerJoinTrigger(
				'player_join',
				[new PlayerFirstJoinCondition()],
				[new PlayerJoinAction()]
			)]
		));

		//Practice::getInstance()->getServer()->getCommandMap()->register('quests', new QuestsCommand());

		$this->questManager->registerQuest(new Quest(
			'ffa_nodebuff_kills_without_pots',
			CollapseTranslationFactory::quest_concrete_ffa_nodebuff_kills_without_pots_name(),
			CollapseTranslationFactory::quest_concrete_ffa_nodebuff_kills_without_pots_description(),
			[new PlayerKillFFANoDebuffWithoutPotsTrigger(
				'ffa_nodebuff_kills_without_pots',
				[new ConditionComposite([new PlayerKillCondition(10), new ItemUsageCondition(VanillaItems::SPLASH_POTION()->getTypeId(), 10), new FreeForAllGamemodeCondition(FreeForAllMode::NoDebuff)], ConditionLogicType::And)],
				[new PlayerKillFFANoDebuffWithoutPotsAction()]
			)],
			'fight'
		));

		$this->questManager->registerQuest(new Quest(
			'ffa_sumo_kills',
			CollapseTranslationFactory::quest_concrete_ffa_sumo_30_kills_name(),
			CollapseTranslationFactory::quest_concrete_ffa_sumo_30_kills_description(),
			[new PlayerSumoKillTrigger(
				'ffa_sumo_kills',
				[new ConditionComposite([new PlayerKillCondition(30), new FreeForAllGamemodeCondition(FreeForAllMode::Sumo)], ConditionLogicType::And)],
				[new PlayerSumoKillAction()]
			)],
			'hold'
		));

		$this->questManager->registerQuest(new Quest(
			'build_place_blocks',
			CollapseTranslationFactory::quest_concrete_ffa_sumo_30_kills_name(),
			CollapseTranslationFactory::quest_concrete_ffa_sumo_30_kills_description(),
			[new PlayerPlaceBlockBuildTrigger(
				'build_place_blocks',
				[new ConditionComposite([new PlayerPlaceBlockCondition(-1, 60), new FreeForAllGamemodeCondition(FreeForAllMode::Build)], ConditionLogicType::And)],
				[new PlayerSumoKillAction()],
			)]
		));

		$this->questManager->loadInCache();
	}

	public function shutdown() : void{

	}

	public function getQuestManager() : QuestManager{
		return $this->questManager;
	}

	public function getTriggers() : array{
		$triggers = [];
		foreach($this->questManager->getAllQuests() as $quest){
			$triggers = array_merge($triggers, $quest->getTriggers());
		}
		return $triggers;
	}
}

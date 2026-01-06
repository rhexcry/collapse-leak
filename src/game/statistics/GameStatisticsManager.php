<?php

declare(strict_types=1);

namespace collapse\game\statistics;

use collapse\game\Game;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use pocketmine\utils\TextFormat;
use function array_map;
use function implode;
use const EOL;

final class GameStatisticsManager{

	public static function simple(Game $game) : self{
		$statisticsManager = new GameStatisticsManager();

		if($game->isDamageDisabled()){
			$statisticsManager->register(new GameStatistics(GameStatistics::COMBO, null));
			$statisticsManager->register(new GameStatistics(GameStatistics::MAX_COMBO, CollapseTranslationFactory::game_statistics_max_combo()));
			$statisticsManager->register(new GameStatistics(GameStatistics::HITS, CollapseTranslationFactory::game_statistics_hits()));
		}else{
			$statisticsManager->register(new GameStatistics(GameStatistics::DAMAGE_DEALT, CollapseTranslationFactory::game_statistics_damage_dealt()));
			$statisticsManager->register(new GameStatistics(GameStatistics::HEALTH_REGENERATED, CollapseTranslationFactory::game_statistics_health_regenerated()));
			$statisticsManager->register(new GameStatistics(GameStatistics::COMBO, null));
			$statisticsManager->register(new GameStatistics(GameStatistics::MAX_COMBO, CollapseTranslationFactory::game_statistics_max_combo()));
			$statisticsManager->register(new GameStatistics(GameStatistics::HITS, CollapseTranslationFactory::game_statistics_hits()));
			$statisticsManager->register(new GameStatistics(GameStatistics::CRITICAL_HITS, CollapseTranslationFactory::game_statistics_critical_hits()));
			$statisticsManager->register(new GameStatistics(GameStatistics::HEALTH, null));
			$statisticsManager->register(new GameStatistics(GameStatistics::HUNGER, null));
		}

		return $statisticsManager;
	}

	/** @var GameStatistics[] */
	private array $types = [];

	/** @var (\Closure(CollapsePlayer $target, CollapsePlayer $player, CollapsePlayer $loser, GameStatistics $statistics) : string)[] */
	private array $formatters = [];

	public function register(GameStatistics $statistics) : void{
		if(isset($this->types[$statistics->getId()])){
			throw new \InvalidArgumentException('Statistics ' . $statistics->getId() . 'already registered');
		}
		$this->types[$statistics->getId()] = $statistics;
	}

	public function get(string $id) : ?GameStatistics{
		return $this->types[$id] ?? null;
	}

	/**
	 * @param \Closure(CollapsePlayer $target, CollapsePlayer $winner, CollapsePlayer $loser, GameStatistics $statistics) : string $formatter
	 */
	public function formatter(string $id, \Closure $formatter) : void{
		$this->formatters[$id] = $formatter;
	}

	public function format(CollapsePlayer $target, CollapsePlayer $winner, CollapsePlayer $loser) : string{
		$result = [];
		foreach($this->types as $statistics){
			if(!$statistics->canBeDisplayed()){
				continue;
			}
			if(isset($this->formatters[$statistics->getId()])){
				$result[] = ($this->formatters[$statistics->getId()])($target, $winner, $loser, $statistics);
			}else{
				$result[] = TextFormat::AQUA . $statistics->get($winner) . ' ' . TextFormat::WHITE . $statistics->translate($target) . ' ' . TextFormat::AQUA . $statistics->get($loser);
			}
		}
		return implode(EOL, $result);
	}

	public function export() : array{
		return array_map(fn(GameStatistics $statistics) : array => $statistics->getData(), $this->types);
	}
}

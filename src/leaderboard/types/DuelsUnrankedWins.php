<?php

declare(strict_types=1);

namespace collapse\leaderboard\types;

use collapse\game\duel\types\DuelMode;
use collapse\game\duel\types\DuelType;
use collapse\leaderboard\Leaderboard;
use collapse\leaderboard\LeaderboardType;
use collapse\leaderboard\mongo\LeaderboardOperation;
use collapse\leaderboard\ProfileLeaderboardEntry;
use collapse\mongo\MongoWrapper;
use collapse\player\profile\Profile;
use collapse\player\profile\ProfileManager;
use collapse\Practice;

final class DuelsUnrankedWins extends Leaderboard{

	/** @var (ProfileLeaderboardEntry[]|null) */
	private ?array $globalEntries = null;

	public function getType() : LeaderboardType{
		return LeaderboardType::DuelsUnrankedWins;
	}

	public function update() : void{
		MongoWrapper::push(new LeaderboardOperation(
			Practice::getDatabaseName(),
			ProfileManager::COLLECTION,
			'duels_unranked_wins',
			10
		))->onResolve(function(array $profiles) : void{
			foreach($profiles as $index => $document){
				$profile = Profile::fromBsonDocument($document);
				$this->globalEntries[$index + 1] = new ProfileLeaderboardEntry(
					$profile,
					$profile->getPlayerName(),
					$profile->getDuelsWins(DuelType::Unranked),
					$index + 1
				);
			}
		});

		foreach(DuelMode::cases() as $mode){
			MongoWrapper::push(new LeaderboardOperation(
				Practice::getDatabaseName(),
				ProfileManager::COLLECTION,
				'duels_unranked_wins_' . $mode->value,
				10
			))->onResolve(function(array $profiles) use ($mode) : void{
				foreach($profiles as $index => $document){
					$profile = Profile::fromBsonDocument($document);
					$this->entries[$mode->value][$index + 1] = new ProfileLeaderboardEntry(
						$profile,
						$profile->getPlayerName(),
						$profile->getDuelsWins(DuelType::Unranked, $mode),
						$index + 1
					);
				}
			});
		}
	}

	public function getGlobalEntries() : ?array{
		return $this->globalEntries;
	}

	public function getEntries(DuelMode $mode) : ?array{
		return $this->entries[$mode->value] ?? null;
	}
}

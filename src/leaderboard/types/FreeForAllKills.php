<?php

declare(strict_types=1);

namespace collapse\leaderboard\types;

use collapse\game\ffa\types\FreeForAllMode;
use collapse\leaderboard\Leaderboard;
use collapse\leaderboard\LeaderboardType;
use collapse\leaderboard\mongo\LeaderboardOperation;
use collapse\leaderboard\ProfileLeaderboardEntry;
use collapse\mongo\MongoWrapper;
use collapse\player\profile\Profile;
use collapse\player\profile\ProfileManager;
use collapse\Practice;

final class FreeForAllKills extends Leaderboard{

	/** @var (ProfileLeaderboardEntry[]|null) */
	private ?array $globalEntries = null;

	public function getType() : LeaderboardType{
		return LeaderboardType::FreeForAllKills;
	}

	public function update() : void{
		MongoWrapper::push(new LeaderboardOperation(
			Practice::getDatabaseName(),
			ProfileManager::COLLECTION,
			'free_for_all_kills',
			10
		))->onResolve(function(array $profiles) : void{
			foreach($profiles as $index => $document){
				$profile = Profile::fromBsonDocument($document);
				$this->globalEntries[$index + 1] = new ProfileLeaderboardEntry(
					$profile,
					$profile->getPlayerName(),
					$profile->getFreeForAllKills(),
					$index + 1
				);
			}
		});

		foreach(FreeForAllMode::cases() as $mode){
			MongoWrapper::push(new LeaderboardOperation(
				Practice::getDatabaseName(),
				ProfileManager::COLLECTION,
				'free_for_all_kills_' . $mode->value,
				10
			))->onResolve(function(array $profiles) use ($mode) : void{
				foreach($profiles as $index => $document){
					$profile = Profile::fromBsonDocument($document);
					$this->entries[$mode->value][$index + 1] = new ProfileLeaderboardEntry(
						$profile,
						$profile->getPlayerName(),
						$profile->getFreeForAllKills($mode),
						$index + 1
					);
				}
			});
		}
	}

	public function getGlobalEntries() : ?array{
		return $this->globalEntries;
	}

	public function getEntries(FreeForAllMode $mode) : ?array{
		return $this->entries[$mode->value] ?? null;
	}
}

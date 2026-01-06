<?php

declare(strict_types=1);

namespace collapse\game\ffa\modes\midfight;

use collapse\game\event\BlockBreakGameEvent;
use collapse\game\event\BlockPlaceGameEvent;
use collapse\game\event\PlayerDeathGameEvent;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\rank\Rank;
use collapse\resourcepack\Font;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;

final readonly class MidFightListener implements Listener{

	public function handleBlockBreak(BlockBreakGameEvent $event) : void{
		if($event->getPlayer()->getGame() instanceof MidFight){
			$event->cancel();
		}
	}

	public function handleBlockPlace(BlockPlaceGameEvent $event) : void{
		if($event->getPlayer()->getGame() instanceof MidFight){
			$event->cancel();
		}
	}

	/**
	 * @priority HIGHEST
	 */
	public function handlePlayerDeathGame(PlayerDeathGameEvent $event) : void{
		$game = $event->getGame();
		if(!$game instanceof MidFight){
			return;
		}

		$player = $event->getPlayer();

		if($event->getCause() === EntityDamageEvent::CAUSE_VOID){
			$killer = $game->getOpponentManager()?->getOpponent($player);
			if($killer === null){
				$event->setDeathMessage(CollapseTranslationFactory::kill_messages_default_void($player->getProfile()->getRank() === Rank::DEFAULT ? $player->getNameWithRankColor() : Font::minecraftColorToUnicodeFont($player->getNameWithRankColor())));
				$player->teleport($game->getConfig()->getSpawnLocation());
				return;
			}
			$statisticsManager = $game->getOpponentManager()?->getStatistics($player);
			$killerName = $killer->getProfile()->getRank() === Rank::DEFAULT ? $killer->getNameWithRankColor() : Font::minecraftColorToUnicodeFont($killer->getNameWithRankColor());
			$playerName = $player->getProfile()->getRank() === Rank::DEFAULT ? $player->getNameWithRankColor() : Font::minecraftColorToUnicodeFont($player->getNameWithRankColor());

			if($statisticsManager !== null){
				$player->sendMessage(Font::SCOREBOARD_LINE . EOL .
					$killerName . TextFormat::RESET . TextFormat::GRAY . ' VS ' . $playerName . EOL .
					$statisticsManager->format($player, $killer, $player) . EOL .
					Font::SCOREBOARD_LINE,
					false);
				$killer->sendMessage(
					Font::SCOREBOARD_LINE . EOL .
					$killerName . TextFormat::RESET . TextFormat::GRAY . ' VS ' . $playerName . EOL .
					$statisticsManager->format($killer, $killer, $player) . EOL .
					Font::SCOREBOARD_LINE,
					false
				);
			}

			$event->setDeathMessage(CollapseTranslationFactory::kill_messages_default_player_void(
				$playerName,
				$killerName
			));
			$player->teleport($game->getConfig()->getSpawnLocation());
			$game->getPlayerManager()->onPlayerKill($player, $killer);
		}
	}
}
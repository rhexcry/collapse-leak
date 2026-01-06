<?php

declare(strict_types=1);

namespace collapse\game\duel\modes\basic;

use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageEvent;

trait KillMessagesHandler{

	public function broadcastDeathMessage(CollapsePlayer $player, int $cause, CollapsePlayer|Entity|null $killer = null) : void{
		$translation = match(true){
			$cause === EntityDamageEvent::CAUSE_VOID && $killer === null => CollapseTranslationFactory::kill_messages_default_void($player->getNameTag()),
			$cause === EntityDamageEvent::CAUSE_VOID && $killer !== null => CollapseTranslationFactory::kill_messages_default_player_void($player->getNameTag(), $killer->getNameTag()),
			$killer instanceof CollapsePlayer => CollapseTranslationFactory::kill_messages_default_player($player->getNameTag(), $killer->getNameTag()),
			default => CollapseTranslationFactory::kill_messages_default_unknown($player->getNameTag())
		};
		$this->duel->getPlayerManager()->broadcastMessage($translation, false);
		$this->duel->getSpectatorManager()->broadcastMessage($translation, false);
	}
}

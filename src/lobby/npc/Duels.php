<?php

declare(strict_types=1);

namespace collapse\lobby\npc;

use collapse\game\duel\form\DuelRequestForm;
use collapse\game\duel\form\DuelsForm;
use collapse\i18n\CollapseTranslationFactory;
use collapse\lobby\npc\animation\DuelsAnimation;
use collapse\npc\animation\NPCAnimation;
use collapse\npc\UpdatableNPC;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\resourcepack\Font;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;

final class Duels extends LobbyNPC implements UpdatableNPC{

	private NPCAnimation $animation;

	public function __construct(Location $location, Skin $skin){
		parent::__construct($location, $skin);
		$this->animation = new DuelsAnimation($this->getId());
	}

	protected function handlePlayerInteract(CollapsePlayer $player) : void{
		if(Practice::getInstance()->getDuelManager()->getQueueManager()->isInQueue($player)){
			$player->sendTranslatedMessage(CollapseTranslationFactory::lobby_npc_duels_already_in_queue());
			return;
		}

		if(!$player->getCurrentForm() instanceof DuelRequestForm){
			$player->sendForm(new DuelsForm($player));
		}
	}

	public function spawnTo(Player $player) : void{
		/** @var CollapsePlayer $player */
		$playing = (string) Practice::getInstance()->getDuelManager()->getPlaying();
		$this->setNameTagFor(
			$player,
			$player->getProfile()->getTranslator()->translate(CollapseTranslationFactory::lobby_npc_duels_menu(Font::text('PLAYING: ') . Font::aqua($playing))));
		parent::spawnTo($player);
		Practice::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($player) : void{
			if($player !== null && $player->isConnected()){
				$player->getNetworkSession()->sendDataPacket($this->animation->encode());
			}
		}), 40);
	}

	public function update() : void{
		$playing = (string) Practice::getInstance()->getDuelManager()->getPlaying();
		foreach($this->getViewers() as $player){
			/** @var CollapsePlayer $player */
			$this->setNameTagFor(
				$player,
				$player->getProfile()->getTranslator()->translate(CollapseTranslationFactory::lobby_npc_duels_menu(Font::text('PLAYING: ') . Font::aqua($playing))));
		}
	}
}

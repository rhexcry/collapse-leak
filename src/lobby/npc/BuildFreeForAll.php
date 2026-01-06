<?php

declare(strict_types=1);

namespace collapse\lobby\npc;

use collapse\game\ffa\types\FreeForAllMode;
use collapse\i18n\CollapseTranslationFactory;
use collapse\lobby\npc\animation\BuildAnimation;
use collapse\npc\animation\NPCAnimation;
use collapse\npc\UpdatableNPC;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\resourcepack\Font;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;

final class BuildFreeForAll extends LobbyNPC implements UpdatableNPC{

	private NPCAnimation $animation;

	public function __construct(Location $location, Skin $skin){
		parent::__construct($location, $skin);
		$this->animation = new BuildAnimation($this->getId());
	}

	protected function handlePlayerInteract(CollapsePlayer $player) : void{
		$plugin = Practice::getInstance();
		if($plugin->getDuelManager()->getQueueManager()->isInQueue($player)){
			return;
		}
		$plugin->getLobbyManager()->removeFromLobby($player);
		$plugin->getFreeForAllManager()->getArena(FreeForAllMode::Build)->getPlayerManager()->addPlayer($player);
	}

	public function spawnTo(Player $player) : void{
		/** @var CollapsePlayer $player */
		$playing = (string) Practice::getInstance()->getFreeForAllManager()->getPlaying(FreeForAllMode::Build);
		$this->setNameTagFor(
			$player,
			$player->getProfile()->getTranslator()->translate(CollapseTranslationFactory::lobby_npc_ffa_build(Font::text('PLAYING: ') . Font::aqua($playing))));
		parent::spawnTo($player);
		Practice::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($player) : void{
			if($player !== null && $player->isConnected()){
				$player->getNetworkSession()->sendDataPacket($this->animation->encode());
			}
		}), 40);
	}

	public function update() : void{
		$playing = (string) Practice::getInstance()->getFreeForAllManager()->getPlaying(FreeForAllMode::Build);
		foreach($this->getViewers() as $player){
			/** @var CollapsePlayer $player */
			$this->setNameTagFor(
				$player,
				$player->getProfile()->getTranslator()->translate(CollapseTranslationFactory::lobby_npc_ffa_build(Font::text('PLAYING: ') . Font::aqua($playing))));
		}
	}

	public function syncNetworkData(EntityMetadataCollection $properties) : void{
		parent::syncNetworkData($properties);
		$properties->setFloat(EntityMetadataProperties::BOUNDING_BOX_HEIGHT, 2.0);
	}
}

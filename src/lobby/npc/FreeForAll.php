<?php

declare(strict_types=1);

namespace collapse\lobby\npc;

use collapse\game\ffa\form\FreeForAllForm;
use collapse\i18n\CollapseTranslationFactory;
use collapse\lobby\npc\animation\FreeForAllAnimation;
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

final class FreeForAll extends LobbyNPC implements UpdatableNPC{

	private NPCAnimation $animation;

	public function __construct(Location $location, Skin $skin){
		parent::__construct($location, $skin);
		$this->animation = new FreeForAllAnimation($this->getId());
	}

	protected function handlePlayerInteract(CollapsePlayer $player) : void{
		$plugin = Practice::getInstance();
		if($plugin->getDuelManager()->getQueueManager()->isInQueue($player)){
			return;
		}
		$player->sendForm(new FreeForAllForm($player));
	}

	/**
	 * @param CollapsePlayer $player
	 */
	public function spawnTo(Player $player) : void{
		$playing = (string) Practice::getInstance()->getFreeForAllManager()->getPlaying();
		$this->setNameTagFor(
			$player,
			$player->getProfile()->getTranslator()->translate(CollapseTranslationFactory::lobby_npc_ffa_menu(Font::text('PLAYING: ') . Font::aqua($playing)))
		);
		parent::spawnTo($player);
		Practice::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($player) : void{
			if($player !== null && $player->isConnected()){
				$player->getNetworkSession()->sendDataPacket($this->animation->encode());
			}
		}), 40);
	}

	public function update() : void{
		/** @var CollapsePlayer $player */
		$playing = (string) Practice::getInstance()->getFreeForAllManager()->getPlaying();
		foreach($this->getViewers() as $player){
			if($player === null || !$player->isConnected()){
				continue;
			}
			$this->setNameTagFor(
				$player,
				$player->getProfile()->getTranslator()->translate(CollapseTranslationFactory::lobby_npc_ffa_menu(Font::text('PLAYING: ') . Font::aqua($playing))));
		}
	}

	public function onPlayerJoin(CollapsePlayer $player) : void{
		$player->getNetworkSession()->sendDataPacket($this->animation->encode());
	}

	public function syncNetworkData(EntityMetadataCollection $properties) : void{
		parent::syncNetworkData($properties);
		$properties->setFloat(EntityMetadataProperties::BOUNDING_BOX_HEIGHT, 2.0);
	}
}

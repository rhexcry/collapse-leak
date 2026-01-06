<?php

declare(strict_types=1);

namespace collapse\lobby\npc;

use collapse\game\duel\types\DuelMode;
use collapse\game\duel\types\DuelType;
use collapse\i18n\CollapseTranslationFactory;
use collapse\lobby\npc\animation\FireballAnimation;
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
use pocketmine\world\particle\FlameParticle;
use function cos;
use function lcg_value;
use function sin;
use const M_PI;

final class FireballFightDuels extends LobbyNPC implements UpdatableNPC{
	protected float $scale = 1.5;

	private int $fireParticlesTicks = 0;

	private NPCAnimation $animation;

	public function __construct(Location $location, Skin $skin){
		parent::__construct($location, $skin);
		$this->animation = new FireballAnimation($this->getId());
	}

	protected function handlePlayerInteract(CollapsePlayer $player) : void{
		$queueManager = Practice::getInstance()->getDuelManager()->getQueueManager();
		if($queueManager->isInQueue($player)){
			$player->sendTranslatedMessage(CollapseTranslationFactory::lobby_npc_duels_already_in_queue());
			return;
		}

		$queueManager->joinSoloQueue($player, DuelMode::FireballFight);
	}

	public function spawnTo(Player $player) : void{
		/** @var CollapsePlayer $player */
		$playing = (string) Practice::getInstance()->getDuelManager()->getPlaying(DuelType::Unranked, DuelMode::FireballFight);
		$this->setNameTagFor(
			$player,
			$player->getProfile()->getTranslator()->translate(CollapseTranslationFactory::lobby_npc_duels_fireball_fight_nametag_online(Font::text('PLAYING: ') . Font::aqua($playing))));
		parent::spawnTo($player);
		Practice::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($player) : void{
			if($player !== null && $player->isConnected()){
				$player->getNetworkSession()->sendDataPacket($this->animation->encode());
			}
		}), 40);
	}

	public function update() : void{
		$playing = (string) Practice::getInstance()->getDuelManager()->getPlaying(null, DuelMode::FireballFight);
		foreach($this->getViewers() as $player){
			/** @var CollapsePlayer $player */
			$this->setNameTagFor(
				$player,
				$player->getProfile()->getTranslator()->translate(CollapseTranslationFactory::lobby_npc_duels_fireball_fight_nametag_online(Font::text('PLAYING: ') . Font::aqua($playing))));
		}
	}

	public function onUpdate(int $currentTick) : bool{
		if(++$this->fireParticlesTicks > 5){
			$this->fireParticlesTicks = 0;
			for($i = 0; $i < 4; ++$i){
				$distance = -0.1 + lcg_value();
				$yaw = $this->location->yaw * M_PI / 180 + (-0.5 + lcg_value()) * 90;
				$x = $distance * cos($yaw);
				$z = $distance * sin($yaw);
				$y = lcg_value() * 0.4 + 0.5;
				$this->getWorld()->addParticle($this->location->add($x, $y, $z), new FlameParticle(), $this->hasSpawned);
			}
		}
		return parent::onUpdate($currentTick);
	}

	public function syncNetworkData(EntityMetadataCollection $properties) : void{
		parent::syncNetworkData($properties);
		$properties->setFloat(EntityMetadataProperties::BOUNDING_BOX_HEIGHT, 1.0);
	}
}

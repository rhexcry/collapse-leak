<?php

declare(strict_types=1);

namespace collapse\npc;

use collapse\npc\event\NPCInteractEvent;
use collapse\player\CollapsePlayer;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\NeverSavedWithChunkEntity;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\SetActorDataPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
use pocketmine\player\Player;
use pocketmine\world\ChunkLoader;
use pocketmine\world\format\Chunk;

abstract class CollapseNPC extends Human implements ChunkLoader, NeverSavedWithChunkEntity{

	private ChunkLoader $chunkLoader;

	private string $name = '';

	public function __construct(Location $location, Skin $skin){
		parent::__construct($location, $skin);
		$this->chunkLoader = new class implements ChunkLoader{};
		$xSpawnChunk = $this->location->getFloorX() >> Chunk::COORD_BIT_SIZE;
		$zSpawnChunk = $this->location->getFloorZ() >> Chunk::COORD_BIT_SIZE;
		$this->getWorld()->registerChunkLoader($this->chunkLoader, $xSpawnChunk, $zSpawnChunk);
		$this->setNoClientPredictions();
	}

	public function canSaveWithChunk() : bool{
		return false;
	}

	protected function getInitialDragMultiplier() : float{
		return 0.0;
	}

	protected function getInitialGravity() : float{
		return 0.0;
	}

	public static function getNetworkTypeId() : string{
		return EntityIds::NPC;
	}

	/**
	 * @param CollapsePlayer $player
	 */
	public function onInteract(Player $player, Vector3 $clickPos) : bool{
		$ev = new NPCInteractEvent($player, $this);
		$ev->call();

		if($ev->isCancelled()){
			return true;
		}

		$this->handlePlayerInteract($player);

		return false;
	}

	public function attack(EntityDamageEvent $source) : void{
		if(!$source instanceof EntityDamageByEntityEvent){
			return;
		}

		if(!($damager = $source->getDamager()) instanceof CollapsePlayer){
			return;
		}

		$ev = new NPCInteractEvent($damager, $this);
		$ev->call();

		if($ev->isCancelled()){
			return;
		}

		$this->handlePlayerInteract($damager);
	}

	public function setName(string $name) : void{
		$this->name = $name;
	}

	public function getName() : string{
		return $this->name;
	}

	public function setNameTagFor(CollapsePlayer $player, string $nametag) : void{
		$properties = clone $this->getNetworkProperties();
		$properties->setString(EntityMetadataProperties::NAMETAG, $nametag);
		$pk = SetActorDataPacket::create(
			$this->getId(),
			$properties->getAll(),
			new PropertySyncData([], []),
			0
		);

		$player->getNetworkSession()->sendDataPacket($pk);
	}

	abstract protected function handlePlayerInteract(CollapsePlayer $player) : void;

	public function onPlayerJoin(CollapsePlayer $player) : void{

	}
}

<?php

declare(strict_types=1);

namespace collapse\inventory;

use collapse\player\CollapsePlayer;
use collapse\Practice;
use pocketmine\block\inventory\BlockInventory;
use pocketmine\block\tile\Chest;
use pocketmine\block\tile\Nameable;
use pocketmine\block\tile\Tile;
use pocketmine\block\tile\TileFactory;
use pocketmine\block\VanillaBlocks;
use pocketmine\inventory\SimpleInventory;
use pocketmine\lang\Translatable;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\Position;

class VirtualDoubleChestInventory extends SimpleInventory implements BlockInventory, VirtualInventory{

	/** @var BlockPosition[] */
	private array $positions = [];

	public function __construct(
		private readonly Translatable $customName,
		private readonly int $delay = 3
	){
		parent::__construct(54);
	}

	public function getHolder() : Position{
		return new Position(
			$this->positions[0]->getX(),
			$this->positions[0]->getY(),
			$this->positions[0]->getZ(),
			null,
		);
	}

	public function open(CollapsePlayer $player) : void{
		parent::onOpen($player);
		$networkSession = $player->getNetworkSession();
		$networkSession->sendDataPacket(UpdateBlockPacket::create(
			$this->positions[] = new BlockPosition(
				$player->getLocation()->getFloorX(),
				$player->getLocation()->getFloorY() - 2,
				$player->getLocation()->getFloorZ()
			),
			$networkSession->getTypeConverter()->getBlockTranslator()->internalIdToNetworkId(VanillaBlocks::CHEST()->getStateId()),
			UpdateBlockPacket::FLAG_NONE,
			UpdateBlockPacket::DATA_LAYER_NORMAL
		), true);
		$networkSession->sendDataPacket(UpdateBlockPacket::create(
			$this->positions[] = new BlockPosition(
				$player->getLocation()->getFloorX() + 1,
				$player->getLocation()->getFloorY() - 2,
				$player->getLocation()->getFloorZ()
			),
			$networkSession->getTypeConverter()->getBlockTranslator()->internalIdToNetworkId(VanillaBlocks::CHEST()->getStateId()),
			UpdateBlockPacket::FLAG_NONE,
			UpdateBlockPacket::DATA_LAYER_NORMAL
		), true);
		$networkSession->sendDataPacket(BlockActorDataPacket::create(
			$this->positions[0],
			new CacheableNbt(CompoundTag::create()
				->setString(Tile::TAG_ID, TileFactory::getInstance()->getSaveId(Chest::class))
				->setInt(Tile::TAG_X, $this->positions[0]->getX())
				->setInt(Tile::TAG_Y, $this->positions[0]->getY())
				->setInt(Tile::TAG_Z, $this->positions[0]->getZ())
				->setInt(Chest::TAG_PAIRX, $this->positions[1]->getX())
				->setInt(Chest::TAG_PAIRZ, $this->positions[1]->getZ())
				->setString(Nameable::TAG_CUSTOM_NAME, $player->getProfile()->getTranslator()->translate($this->customName))
			)
		), true);
		$networkSession->sendDataPacketWithReceipt(BlockActorDataPacket::create(
			$this->positions[1],
			new CacheableNbt(CompoundTag::create()
				->setString(Tile::TAG_ID, TileFactory::getInstance()->getSaveId(Chest::class))
				->setInt(Tile::TAG_X, $this->positions[1]->getX())
				->setInt(Tile::TAG_Y, $this->positions[1]->getY())
				->setInt(Tile::TAG_Z, $this->positions[1]->getZ())
				->setInt(Chest::TAG_PAIRX, $this->positions[0]->getX())
				->setInt(Chest::TAG_PAIRZ, $this->positions[0]->getZ())
				->setString(Nameable::TAG_CUSTOM_NAME, $player->getProfile()->getTranslator()->translate($this->customName))
			)
		))->onCompletion(function() use ($player) : void{
			//Hack to resolve a pairing update on clientside after receive last block actor (Bedrock Edition moment)
			Practice::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($player) : void{
				if($player === null || !$player->isConnected()){
					return;
				}
				$player->setCurrentWindow($this);
			}), $this->delay);
		}, fn() => null);
	}

	public function onClose(Player $who) : void{
		foreach($this->positions as $position){
			$who->getNetworkSession()->sendDataPacket(UpdateBlockPacket::create(
				$position,
				$who->getNetworkSession()->getTypeConverter()->getBlockTranslator()->internalIdToNetworkId($who->getWorld()->getBlockAt(
					$position->getX(),
					$position->getY(),
					$position->getZ()
				)->getStateId()),
				UpdateBlockPacket::FLAG_NONE,
				UpdateBlockPacket::DATA_LAYER_NORMAL
			));
		}
		parent::onClose($who);
	}
}

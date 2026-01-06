<?php

declare(strict_types=1);

namespace collapse;

use collapse\player\CollapsePlayer;
use collapse\utils\ItemUtils;
use pocketmine\entity\animation\ArmSwingAnimation;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCreationEvent;
use pocketmine\event\player\PlayerDataSaveEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMissSwingEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\BiomeDefinitionListPacket;
use pocketmine\network\mcpe\protocol\ItemRegistryPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\SetPlayerInventoryOptionsPacket;
use pocketmine\network\mcpe\protocol\types\inventory\InventoryLayout;
use pocketmine\network\mcpe\protocol\types\inventory\InventoryLeftTab;
use pocketmine\network\mcpe\protocol\types\inventory\InventoryRightTab;

final class PracticeListener implements Listener{

	private ?ItemRegistryPacket $cachedItemRegistryPacket = null;

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerCreation(PlayerCreationEvent $event) : void{
		$event->setPlayerClass(CollapsePlayer::class);
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerJoin(PlayerJoinEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();

		$event->setJoinMessage('');
		//Force disable asshole inventory layout
		$player->getNetworkSession()->sendDataPacket(SetPlayerInventoryOptionsPacket::create(
			InventoryLeftTab::NONE,
			InventoryRightTab::ARMOR,
			false,
			InventoryLayout::SURVIVAL,
			InventoryLayout::SURVIVAL
		));
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerQuit(PlayerQuitEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		$event->setQuitMessage('');
		$player->setScoreboard(null);
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerDataSave(PlayerDataSaveEvent $event) : void{
		$event->cancel();
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerMissSwing(PlayerMissSwingEvent $event) : void{
		$event->cancel();
		$player = $event->getPlayer();
		$player->broadcastAnimation(new ArmSwingAnimation($player), $player->getViewers());
	}

	/**
	 * @priority LOWEST
	 */
	public function handleDataPacketSend(DataPacketSendEvent $event) : void{
		foreach($event->getPackets() as $packet){
			if($packet instanceof BiomeDefinitionListPacket){
				foreach($event->getTargets() as $session){
					// https://github.com/NetherGamesMC/PocketMine-MP/blob/9bb498806586517473012b2e7fab12402dcd3cd5/src/network/mcpe/handler/PreSpawnPacketHandler.php#L117-L120
					$protocolId = $session->getProtocolId();
					if($protocolId >= ProtocolInfo::PROTOCOL_1_21_60){
						return;
					}

					if($this->cachedItemRegistryPacket === null){
						$this->cachedItemRegistryPacket = ItemRegistryPacket::create(ItemUtils::getItemTypeEntries());
					}

					$session->sendDataPacket($this->cachedItemRegistryPacket);
					break;
				}
			}
			/*if($packet instanceof StartGamePacket){
				$packet->levelSettings->experiments = new Experiments([
					'experimental_graphics' => true,
					'data_driven_items' => true
				], false);
			}elseif($packet instanceof ResourcePackStackPacket){
				$packet->experiments = new Experiments([
					'experimental_graphics' => true,
					'data_driven_items' => true
				], false);
			}elseif($packet instanceof ResourcePacksInfoPacket){
				(function() : void{
					$this->forceDisableVibrantVisuals = false;
				})->bindTo($packet, $packet)(); //This idiot really make this private?
			}*/
		}
	}
}

<?php

declare(strict_types=1);

namespace collapse\lobby;

use collapse\game\duel\queue\event\PlayerLeaveQueueEvent;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\player\profile\event\ProfileLoadedEvent;
use collapse\player\rank\event\ProfileRankChangeEvent;
use collapse\player\rank\Rank;
use collapse\Practice;
use collapse\resourcepack\Font;
use collapse\world\sound\MinecraftSound;
use collapse\world\sound\MinecraftSoundNames;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockUpdateEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\permission\DefaultPermissions;

final readonly class LobbyListener implements Listener{

	public function __construct(
		private LobbyManager $lobbyManager
	){}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerJoin(PlayerJoinEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		$this->lobbyManager->sendToLobby($player);
		$player->spawnToAll();
	}

	/**
	 * @priority LOWEST
	 */
	public function handleBlockUpdate(BlockUpdateEvent $event) : void{
		$block = $event->getBlock();
		if($block->getPosition()->getWorld() === $this->lobbyManager->getSpawnLocation()->getWorld()){
			$event->cancel();
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerInteract(PlayerInteractEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		if($this->lobbyManager->isInLobby($player) && !Practice::isTestServer()){
			$event->cancel();
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerExhaust(PlayerExhaustEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		if($this->lobbyManager->isInLobby($player)){
			$event->cancel();
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handleBlockBreak(BlockBreakEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		if($this->lobbyManager->isInLobby($player) && !Practice::isTestServer()){
			$event->cancel();
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handleBlockPlace(BlockPlaceEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		if($this->lobbyManager->isInLobby($player) && !Practice::isTestServer()){
			$event->cancel();
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handleEntityDamage(EntityDamageEvent $event) : void{
		$player = $event->getEntity();
		if($player instanceof CollapsePlayer && $this->lobbyManager->isInLobby($player)){
			$event->cancel();
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerDropItem(PlayerDropItemEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		if($this->lobbyManager->isInLobby($player)){
			$event->cancel();
		}
	}

	/**
	 * @priority LOW
	 */
	public function handleInventoryTransaction(InventoryTransactionEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getTransaction()->getSource();
		if($this->lobbyManager->isInLobby($player)){
			if(Practice::getInstance()->getKitEditorManager()->isEditing($player)){
				return;
			}
			if(!(Practice::isTestServer() && $player->isCreative() && $player->hasPermission(DefaultPermissions::ROOT_OPERATOR))){
				$event->cancel();
			}
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handleEntityDamageByEntity(EntityDamageByEntityEvent $event) : void{
		$player = $event->getEntity();
		if($player instanceof CollapsePlayer && $this->lobbyManager->isInLobby($player)){
			$event->cancel();
			return;
		}
		$attacker = $event->getDamager();
		if($attacker instanceof CollapsePlayer && $this->lobbyManager->isInLobby($attacker)){
			$event->cancel();
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerLeaveQueue(PlayerLeaveQueueEvent $event) : void{
		$player = $event->getPlayer();
		if($this->lobbyManager->isInLobby($player)){
			$player->getWorld()->addSound($player->getLocation(), new MinecraftSound(MinecraftSoundNames::BLOCK_ITEMFRAME_PLACE), [$player]);
			$player->sendTranslatedMessage(CollapseTranslationFactory::queue_cancelled());
			$this->lobbyManager->setProperties($player);
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handleProfileRankChange(ProfileRankChangeEvent $event) : void{
		$player = $event->getProfile()->getPlayer();
		if($player === null){
			return;
		}

		if($this->lobbyManager->isInLobby($player)){
			$player->setAllowFlight($event->getRank() !== Rank::DEFAULT);
		}

		if($event->getRank() === Rank::DEFAULT){
			$player->setFlying(false);
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handleProfileLoaded(ProfileLoadedEvent $event) : void{
		$player = $event->getProfile()->getPlayer();
		$player?->sendTranslatedMessage(CollapseTranslationFactory::welcome_message(Font::minecraftColorToUnicodeFont($player->getNameWithRankColor())));
	}
}

<?php

declare(strict_types=1);

namespace collapse\system\kiteditor;

use collapse\game\kit\Kit;
use collapse\game\kit\KitCollection;
use collapse\game\kit\Kits;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\system\kiteditor\command\KitCommand;
use collapse\system\kiteditor\layout\KitLayout;

class KitEditorManager{

	/**
	 * @var array<string, KitEditorSession>
	 */
	private array $sessions = [];

	public function __construct(
		private readonly Practice $plugin
	){
		$this->plugin->getServer()->getCommandMap()->register('collapse', new KitCommand($this));
		$this->plugin->getServer()->getPluginManager()->registerEvents(new KitEditorListener($this), $this->plugin);
	}

	public function startEditing(CollapsePlayer $player, Kit $kit) : void{
		if($this->isEditing($player)){
			throw new \RuntimeException('Kit editor session for ' . $player->getName() . ' already exists.');
		}

		$queueManager = Practice::getInstance()->getDuelManager()->getQueueManager();
		if($queueManager->isInQueue($player)){
			$queueManager->onPlayerLeaveQueue($player);
		}

		Practice::getInstance()->getLobbyManager()->sendToLobby($player);
		$player->setNoClientPredictions();

		$kitContents = Kits::get($kit)->getContents();
		$oldLayout = $player->getProfile()->getKitLayout($kit) ?? KitLayout::fromItems($kitContents);
		$this->sessions[$player->getXuid()] = new KitEditorSession($player, $oldLayout, $kit);

		$player->getInventory()->clearAll();
		$player->getCursorInventory()->setContents([]);
		$player->getOffHandInventory()->setContents([]);

		$equippedKit = $this->equipLayoutOnKit($oldLayout, $kit);
		$player->getInventory()->setContents($equippedKit->getContents());
		$player->getArmorInventory()->setContents($equippedKit->getArmorContents());

		if($player->isConnected()){
			$player->sendTranslatedMessage(CollapseTranslationFactory::kit_editor_editing_started($kit->toDisplayName()));
		}
	}

	public function isEditing(CollapsePlayer $player) : bool{
		return isset($this->sessions[$player->getXuid()]);
	}

	public function stopEditing(CollapsePlayer $player, bool $saveChanges = false) : void{
		if(!$this->isEditing($player)){
			return;
		}

		$session = $this->sessions[$player->getXuid()];

		if($player->isConnected()){
			$player->getInventory()->clearAll();
			$player->getArmorInventory()->clearAll();
			$player->getCursorInventory()->clearAll();
			$player->getOffHandInventory()->clearAll();
			$player->sendTranslatedMessage(CollapseTranslationFactory::kit_editor_editing_stopped($session->getEditingKit()->toDisplayName()));
			Practice::getInstance()->getLobbyManager()->sendToLobby($player);
			$player->setNoClientPredictions(false);
		}

		if($saveChanges){
			$profile = $player->getProfile();
			$profile->saveKitLayout($session->getNewLayout(), $session->getEditingKit());
			$profile->save();
			$player->sendTranslatedMessage(CollapseTranslationFactory::command_kit_save_successfully($session->getEditingKit()->toDisplayName()));
		}

		unset($this->sessions[$player->getXuid()]);
	}

	public function getSession(CollapsePlayer $player) : ?KitEditorSession{
		return $this->sessions[$player->getXuid()] ?? null;
	}

	public function updateSession(KitEditorSession $session) : void{
		$this->sessions[$session->getPlayer()->getXuid()] = $session;
	}

	public function equipLayoutOnKit(KitLayout $layout, Kit $kit) : KitCollection{
		$kitCollection = clone Kits::get($kit);
		$kitContents = $kitCollection->getContents();
		$layoutContents = $layout->getContents();

		$itemSlotsMap = [];
		foreach($layoutContents as $slot => $itemData){
			$itemSlotsMap[$itemData['id']][] = $slot;
		}

		$assignedSlots = [];
		foreach($kitContents as $item){
			$itemId = $item->getTypeId();

			if(!empty($itemSlotsMap[$itemId])){
				$slot = array_shift($itemSlotsMap[$itemId]);
				$assignedSlots[$slot] = $item;
			}
		}

		$kitCollection->setContents($assignedSlots);

		return $kitCollection;
	}
}
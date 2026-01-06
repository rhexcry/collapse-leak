<?php

declare(strict_types=1);

namespace collapse\system\kiteditor;

use collapse\game\kit\Kit;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\system\kiteditor\layout\KitLayout;

final class KitEditorSession{

	private KitLayout $newLayout;

	public function __construct(
		private readonly CollapsePlayer $player,
		private readonly KitLayout      $oldLayout,
		private readonly Kit 			$editingKit
	){
		$this->newLayout = $this->oldLayout;
	}

	public function updateNewLayout(array $inventory) : void{
		$this->newLayout = KitLayout::fromItems($inventory);
		Practice::getInstance()->getKitEditorManager()->updateSession($this);
	}

	public function getOldLayout() : KitLayout{
		return $this->oldLayout;
	}

	public function getNewLayout() : KitLayout{
		return $this->newLayout;
	}

	public function getEditingKit() : Kit{
		return $this->editingKit;
	}

	public function getPlayer() : CollapsePlayer{
		return $this->player;
	}
}
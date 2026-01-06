<?php

declare(strict_types=1);

namespace collapse\cosmetics;

use collapse\cosmetics\capes\CapesManager;
use collapse\cosmetics\effects\EffectsManager;
use collapse\cosmetics\potion\PotionColorManager;
use collapse\cosmetics\tags\ChatTagsManager;
use collapse\Practice;

final readonly class CosmeticsManager{

	private CapesManager $capesManager;

	private ChatTagsManager $chatTagsManager;

	private EffectsManager $effectsManager;

	private PotionColorManager $potionColorManager;

	public function __construct(
		private Practice $plugin
	){
		$this->capesManager = new CapesManager($this->plugin);
		$this->chatTagsManager = new ChatTagsManager($this->plugin);
		$this->effectsManager = new EffectsManager($this->plugin);
		$this->potionColorManager = new PotionColorManager($this->plugin);
	}

	public function getPlugin() : Practice{
		return $this->plugin;
	}

	public function getCapesManager() : CapesManager{
		return $this->capesManager;
	}

	public function getChatTagsManager() : ChatTagsManager{
		return $this->chatTagsManager;
	}

	public function getEffectsManager() : EffectsManager{
		return $this->effectsManager;
	}

	public function getPotionColorManager() : PotionColorManager{
		return $this->potionColorManager;
	}
}

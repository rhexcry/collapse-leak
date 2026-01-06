<?php

declare(strict_types=1);

namespace collapse\cosmetics\potion;

use collapse\player\profile\Profile;
use collapse\Practice;

final readonly class PotionColorManager{

	public function __construct(
		private Practice $plugin
	){
		$this->plugin->getServer()->getPluginManager()->registerEvents(new PotionColorListener(), $this->plugin);
	}

	public function getPlugin() : Practice{
		return $this->plugin;
	}

	public function onChangePotionColor(Profile $profile, ?PotionColor $color) : void{
		$profile->setPotionColor($color);
		$profile->save();
	}
}
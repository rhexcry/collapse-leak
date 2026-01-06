<?php

declare(strict_types=1);

namespace collapse\cosmetics\capes;

use collapse\player\profile\Profile;
use collapse\Practice;
use pocketmine\entity\Skin;
use Symfony\Component\Filesystem\Path;
use function is_dir;
use function mkdir;

final readonly class CapesManager{

	public function __construct(
		private Practice $plugin
	){
		if(!is_dir(Path::join($this->plugin->getDataFolder(), CapeImages::IMAGES_LOCATION))){
			mkdir(Path::join($this->plugin->getDataFolder(), CapeImages::IMAGES_LOCATION), 0775, true);
		}
		foreach(Cape::cases() as $cape){
			$this->plugin->saveResource(Path::join(CapeImages::IMAGES_LOCATION, $cape->toImage()), true);
		}
		CapeSkins::init();
		$this->plugin->getServer()->getPluginManager()->registerEvents(new CapesListener($this), $this->plugin);
	}

	public function getPlugin() : Practice{
		return $this->plugin;
	}

	public function onChangeCape(Profile $profile, ?Cape $cape) : void{
		$profile->setCape($cape);
		$profile->save();
		$this->setCapeOnSkin($profile, $cape);
	}

	public function getSkinWithCape(Skin $skin, ?Cape $cape) : Skin{
		try{
			return new Skin(
				$skin->getSkinId(),
				$skin->getSkinData(),
				$cape === null ? '' : CapeSkins::getImage($cape)->getData(),
				$skin->getGeometryName(),
				$skin->getGeometryData()
			);
		}catch(\JsonException){
			return $skin;
		}
	}

	public function setCapeOnSkin(Profile $profile, ?Cape $cape) : void{
		$player = $profile->getPlayer();
		if($player === null){
			return;
		}
		$player->setSkin($this->getSkinWithCape($player->getSkin(), $cape));
		$player->sendSkin();
	}
}

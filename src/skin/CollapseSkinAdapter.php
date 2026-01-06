<?php

declare(strict_types=1);

namespace collapse\skin;

use collapse\Practice;
use collapse\utils\SkinUtils;
use pocketmine\entity\InvalidSkinException;
use pocketmine\entity\Skin;
use pocketmine\network\mcpe\convert\LegacySkinAdapter;
use pocketmine\network\mcpe\protocol\types\skin\SkinData;
use Symfony\Component\Filesystem\Path;
use function is_array;
use function is_string;
use function json_decode;

final class CollapseSkinAdapter extends LegacySkinAdapter{
	private const string DEFAULT_SKIN = 'default.png';

	private Skin $defaultSkin;

	public function __construct(
		private readonly Practice $plugin
	){
		$this->plugin->saveResource(Path::join('skins', self::DEFAULT_SKIN), true);
		$this->defaultSkin = new Skin(
			'Standard_Custom',
			SkinUtils::PNG2Data(Path::join($this->plugin->getDataFolder(), 'skins', self::DEFAULT_SKIN))
		);
	}

	public function fromSkinData(SkinData $data) : Skin{
		if($data->isPersona() || $data->isPremium()){
			return clone $this->defaultSkin;
		}

		$resourcePatch = json_decode($data->getResourcePatch(), true);
		if(is_array($resourcePatch) && isset($resourcePatch['geometry']['default']) && is_string($resourcePatch['geometry']['default'])){
			$geometryName = $resourcePatch['geometry']['default'];
			if(!($geometryName === 'geometry.humanoid.custom' || $geometryName === 'geometry.humanoid.customSlim')){
				return clone $this->defaultSkin;
			}
		}else{
			throw new InvalidSkinException('Missing geometry name field');
		}

		return parent::fromSkinData($data);
	}
}

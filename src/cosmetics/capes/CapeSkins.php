<?php

declare(strict_types=1);

namespace collapse\cosmetics\capes;

use collapse\Practice;
use collapse\utils\SkinUtils;
use pocketmine\network\mcpe\protocol\types\skin\SkinImage;
use Symfony\Component\Filesystem\Path;

final class CapeSkins{

	private static ?array $skins = null;

	private static function capeToImage(Cape $cape) : SkinImage{
		return new SkinImage(
			32,
			64,
			SkinUtils::PNG2Data(Path::join(Practice::getInstance()->getDataFolder(), CapeImages::IMAGES_LOCATION, $cape->toImage()))
		);
	}

	private static function register(Cape $cape) : void{
		self::$skins[$cape->value] = self::capeToImage($cape);
	}

	public static function init() : void{
		foreach(Cape::cases() as $cape){
			self::register($cape);
		}
	}

	public static function getImage(Cape $cape) : ?SkinImage{
		return self::$skins[$cape->value] ?? null;
	}
}

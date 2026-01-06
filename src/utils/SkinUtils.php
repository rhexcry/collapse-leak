<?php

declare(strict_types=1);

namespace collapse\utils;

use function chr;
use function file_exists;
use function fopen;
use function imagealphablending;
use function imagecolorat;
use function imagecreatefrompng;
use function imagecreatetruecolor;
use function imagepalettetotruecolor;
use function imagepng;
use function imagesavealpha;
use function imagesetpixel;
use function imagesx;
use function imagesy;
use function pathinfo;
use function rewind;
use function stream_get_contents;
use function strlen;
use function substr;
use function unpack;
use const PATHINFO_EXTENSION;

final readonly class SkinUtils{

	public const array SIZES = [
		64 * 32 * 4 => [64, 32],
		64 * 64 * 4 => [64, 64],
		128 * 64 * 4 => [64, 128],
		128 * 128 * 4 => [128, 128]
	];

	private function __construct(){}

	private static function explodeSkinToPixel(string $skinData, \Closure $action) : \GdImage{
		$skinSize = SkinUtils::SIZES[strlen($skinData)];
		$image = imagecreatetruecolor($skinSize[0], $skinSize[1]);
		imagealphablending($image, false);
		imagesavealpha($image, true);

		for($y = 0, $height = imagesy($image); $y < $height; $y++){
			for($x = 0, $width = imagesx($image); $x < $width; $x++){
				$color = unpack('N', substr($skinData, ($y * $height * 4) + ($x * 4), 4))[1];
				$action($image, ($color >> 8) | (((~($color & 0xff) & 0xff) >> 1) << 24), $x, $y, $width, $height);
			}
		}

		return $image;
	}

	public static function skinToPNG(string $skinData) : \GdImage{
		return self::explodeSkinToPixel($skinData, static function(\GdImage $image, int $color, int $x, int $y) : void{
			imagesetpixel($image, $x, $y, $color);
		});
	}

	public static function skinToPNGdata(string $skinData) : string{
		$stream = fopen('php://memory', 'r+');
		imagepng(self::skinToPNG($skinData), $stream);
		rewind($stream);
		return stream_get_contents($stream);
	}

	public static function PNG2Data(\GdImage|string $skinImage) : ?string{
		if(!($skinImage instanceof \GdImage)){
			if(!file_exists($skinImage)){
				return null;
			}

			if(pathinfo($skinImage, PATHINFO_EXTENSION) !== 'png'){
				return null;
			}

			$skinImage = imagecreatefrompng($skinImage);
			if($skinImage === false){
				return null;
			}
		}

		$skinSize = self::SIZES[imagesx($skinImage) * imagesy($skinImage) * 4] ?? null;
		if($skinSize === null){
			return null;
		}

		imagepalettetotruecolor($skinImage);

		$skinData = '';
		for($y = 0, $skinHeight = $skinSize[1]; $y < $skinHeight; $y++){
			for($x = 0, $skinWidth = $skinSize[0]; $x < $skinWidth; $x++){
				$color = imagecolorat($skinImage, $x, $y);
				$skinData .=
					chr($color >> 16 & 0xff) .
					chr($color >> 8 & 0xff) .
					chr($color >> 0 & 0xff) .
					chr(($color >> 24 & 0xff) === 0x7f ? 0x00 : 0xff);
			}
		}

		return $skinData;
	}
}

<?php

declare(strict_types=1);

namespace collapse\skin;

use collapse\Practice;
use collapse\skin\geometry\SkinGeometry;
use collapse\utils\SkinUtils;
use pocketmine\entity\Skin;
use pocketmine\utils\Filesystem;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Filesystem\Path;
use function imagecolorat;
use function imagecreatefromstring;
use function imagesx;
use function imagesy;
use function json_decode;
use function pack;
use function str_contains;
use const JSON_THROW_ON_ERROR;

final readonly class SkinFactory{

	public function createSkin(
		string $skinData,
		string $geometryName,
		string $geometryData,
		?string $capeData = null,
		?string $skinId = null
	) : Skin{
		$skinId ??= Uuid::uuid4()->toString();

		return new Skin(
			$skinId,
			$skinData,
			$capeData ?? '',
			$geometryName,
			$geometryData
		);
	}

	public function loadFromFile(string $path) : Skin{
		$path = $this->getResourcePath($path);
		$content = Filesystem::fileGetContents($path);

		return match(true){
			str_contains($path, '.json') => $this->fromJson($content),
			str_contains($path, '.png') => $this->createSkin(SkinUtils::PNG2Data($path), 'geometry.humanoid.custom', $this->getDefaultGeometry()),
			default => throw new \InvalidArgumentException('Unsupported skin format')
		};
	}

	public function fromJson(string $json) : Skin{
		$data = json_decode($json, true, flags: JSON_THROW_ON_ERROR);

		return $this->createSkin(
			$data['skinData'] ?? '',
			$data['geometryName'] ?? '',
			$data['geometryData'] ?? '',
			$data['capeData'] ?? null,
			$data['skinId'] ?? null
		);
	}

	public function fromPng(string $pngData) : Skin{
		$image = imagecreatefromstring($pngData);
		$skinData = '';

		for($y = 0; $y < imagesy($image); $y++){
			for($x = 0; $x < imagesx($image); $x++){
				$color = imagecolorat($image, $x, $y);
				$skinData .= pack('c', ($color >> 16) & 0xFF) // R
					. pack('c', ($color >> 8) & 0xFF)  // G
					. pack('c', $color & 0xFF)          // B
					. pack('c', ($color >> 24) & 0xFF); // A
			}
		}

		return $this->createSkin($skinData, 'geometry.humanoid.custom', $this->getDefaultGeometry());
	}

	public function withGeometry(Skin $skin, SkinGeometry $geometry) : Skin{
		return $this->createSkin(
			$skin->getSkinData(),
			$geometry->getName(),
			$geometry->getData(),
			$skin->getCapeData(),
			$skin->getSkinId()
		);
	}

	public function getDefaultGeometry() : string{
		return (new SkinGeometry('geometry.humanoid.custom', 'default/geometry.humanoid.custom.json'))->getData();
	}

	private function getResourcePath(string $path) : string{
		return Path::join(Practice::getInstance()->getDataFolder(), 'skins', $path);
	}
}

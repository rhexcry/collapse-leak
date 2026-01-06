<?php

declare(strict_types=1);

namespace collapse\skin\geometry;

use function array_map;
use function json_encode;
use const JSON_THROW_ON_ERROR;

final class GeometryBuilder{

	private array $bones = [];
	private array $textures = [];
	private float $visibleBoundsWidth = 0.0;
	private float $visibleBoundsHeight = 0.0;
	private array $textureSize = [64, 64];

	public function withBone(string $name, array $cubes) : self{
		$this->bones[] = [
			'name' => $name,
			'cubes' => $this->processCubes($cubes)
		];
		return $this;
	}

	public function withTexture(string $name, string $texture) : self{
		$this->textures[$name] = $texture;
		return $this;
	}

	public function withVisibleBounds(float $width, float $height) : self{
		$this->visibleBoundsWidth = $width;
		$this->visibleBoundsHeight = $height;
		return $this;
	}

	public function withTextureSize(int $width, int $height) : self{
		$this->textureSize = [$width, $height];
		return $this;
	}

	public function build(string $geometryName) : SkinGeometry{
		$geometry = [
			'format_version' => '1.12.0',
			'minecraft:geometry' => [[
				'description' => [
					'identifier' => $geometryName,
					'texture_width' => $this->textureSize[0],
					'texture_height' => $this->textureSize[1],
					'visible_bounds_width' => $this->visibleBoundsWidth,
					'visible_bounds_height' => $this->visibleBoundsHeight,
					'visible_bounds_offset' => [0, 0, 0]
				],
				'bones' => $this->bones
			]]
		];

		return new SkinGeometry(
			$geometryName,
			json_encode($geometry, JSON_THROW_ON_ERROR)
		);
	}

	private function processCubes(array $cubes) : array{
		return array_map(fn(array $cube) => [
			'origin' => $cube['origin'] ?? [0, 0, 0],
			'size' => $cube['size'] ?? [1, 1, 1],
			'uv' => $cube['uv'] ?? [0, 0],
			'rotation' => $cube['rotation'] ?? [0, 0, 0],
			'inflate' => $cube['inflate'] ?? 0.0
		], $cubes);
	}
}

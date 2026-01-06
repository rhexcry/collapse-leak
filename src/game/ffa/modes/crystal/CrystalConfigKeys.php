<?php

declare(strict_types=1);

namespace collapse\game\ffa\modes\crystal;

final readonly class CrystalConfigKeys{

	public const string SPAWN_BOUNDS = 'spawnBounds';
	public const string SPAWN_BOUNDS_MIN_X = 'minX';
	public const string SPAWN_BOUNDS_MIN_Y = 'minY';
	public const string SPAWN_BOUNDS_MIN_Z = 'minZ';
	public const string SPAWN_BOUNDS_MAX_X = 'maxX';
	public const string SPAWN_BOUNDS_MAX_Y = 'maxY';
	public const string SPAWN_BOUNDS_MAX_Z = 'maxZ';

	private function __construct(){}
}

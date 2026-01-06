<?php

declare(strict_types=1);

namespace collapse\game\ffa;

final readonly class FreeForAllConfigKeys{

	public const string MODE = 'mode';
	public const string SPAWN_LOCATION = 'spawnLocation';
	public const string SPAWN_LOCATION_WORLD = 'world';
	public const string SPAWN_LOCATION_X = 'x';
	public const string SPAWN_LOCATION_Y = 'y';
	public const string SPAWN_LOCATION_Z = 'z';
	public const string SPAWN_LOCATION_YAW = 'yaw';
	public const string SPAWN_LOCATION_PITCH = 'pitch';

	private function __construct(){}
}

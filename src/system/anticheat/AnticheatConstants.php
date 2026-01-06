<?php

declare(strict_types=1);

namespace collapse\system\anticheat;

final class AnticheatConstants {

	public const int MAX_GROUND_DIFF = 1;
	public const int MAX_AIR_TICKS = 40;


	public const float MAX_DISTANCE = 4.3;
	public const float SURVIVAL_MAX_DISTANCE = 3.0;
	public const float CREATIVE_MAX_DISTANCE = 8.3;
	public const float MAX_REACH_EYE_DISTANCE = 3.0;
	public const float DEFAULT_EYE_DISTANCE = 0.0041;
	public const float SPRINTING_EYE_DISTANCE = 0.97;
	public const float NOT_SPRINTING_EYE_DISTANCE = 0.87;
	public const float DAMAGER_SPRINTING_EYE_DISTANCE = 0.77;
	public const float DAMAGER_NOT_SPRINTING_EYE_DISTANCE = 0.67;
	public const float REACH_EYE_LIMIT = 3.0;

}
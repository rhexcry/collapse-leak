<?php

declare(strict_types=1);

namespace collapse\player\client;

use pocketmine\network\mcpe\protocol\types\InputMode;

final readonly class InputModeUtils{

	private function __construct(){}

	public static function toDisplayName(int $inputMode) : string{
		return match($inputMode){
			InputMode::MOUSE_KEYBOARD => 'Mouse & Keyboard',
			InputMode::TOUCHSCREEN => 'Touchscreen',
			InputMode::GAME_PAD => 'Game Pad',
			InputMode::MOTION_CONTROLLER => 'Motion Controller',
			default => 'Unknown'
		};
	}
}

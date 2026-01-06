<?php

declare(strict_types=1);

namespace collapse;

use pocketmine\utils\TextFormat;

final readonly class PracticeConstants{

	private function __construct(){}

	public const string STORE_LINK = 'collapsemc.com';

	public const string CHAT_MESSAGE_PREFIX = ' ';

	public const string ITEM_LORE = TextFormat::RESET . TextFormat::BOLD . TextFormat::AQUA . 'Collapse' . TextFormat::RESET . TextFormat::GRAY . ' clps.gg';

	public const string PLAYER_JOIN_MESSAGE = TextFormat::GREEN . '+' . TextFormat::GRAY . ' | ';

	public const string PLAYER_QUIT_MESSAGE = TextFormat::RED . '-' . TextFormat::GRAY . ' | ';
}

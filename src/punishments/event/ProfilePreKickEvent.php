<?php

declare(strict_types=1);

namespace collapse\punishments\event;

use collapse\player\profile\Profile;
use pocketmine\command\CommandSender;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;

final class ProfilePreKickEvent extends Event{
	use CancellableTrait;

	public function __construct(
		private readonly Profile $profile,
		private readonly string $reason,
		private readonly ?CommandSender $sender
	){}

	public function getProfile() : Profile{
		return $this->profile;
	}

	public function getReason() : string{
		return $this->reason;
	}

	public function getSender() : ?CommandSender{
		return $this->sender;
	}
}
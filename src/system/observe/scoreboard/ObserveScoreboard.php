<?php

declare(strict_types=1);

namespace collapse\system\observe\scoreboard;

use collapse\i18n\CollapseTranslationFactory;
use collapse\player\client\DeviceUtils;
use collapse\player\client\InputModeUtils;
use collapse\player\CollapsePlayer;
use collapse\player\scoreboard\CollapseScoreboard;
use collapse\PracticeConstants;
use collapse\resourcepack\Font;
use collapse\system\observe\ObserveManager;
use pocketmine\utils\TextFormat;

final class ObserveScoreboard extends CollapseScoreboard{

	public function __construct(
		CollapsePlayer $player,
		private readonly ObserveManager $observeManager
	){
		parent::__construct($player);
	}

	public function setUp() : void{
		$session = $this->observeManager->getSession($this->player);
		if($session === null){
			return;
		}
		$target = $session->getTarget();
		$this->setLines([
			1 => TextFormat::BLACK . Font::SCOREBOARD_LINE,
			2 => CollapseTranslationFactory::observe_scoreboard_player(),
			3 => CollapseTranslationFactory::observe_scoreboard_player_name($target->getNameWithRankColor()),

			6 => CollapseTranslationFactory::observe_scoreboard_os(DeviceUtils::toDisplayName($target->getProfile()->getDeviceOS())),
			7 => CollapseTranslationFactory::observe_scoreboard_input(),

			9 => null,
			10 => ' ' . Font::bold(PracticeConstants::STORE_LINK),
			11 => TextFormat::GRAY . Font::SCOREBOARD_LINE
		]);
		$this->onUpdate();
	}

	public function onUpdate() : void{
		$session = $this->observeManager->getSession($this->player);
		$target = $session?->getTarget();
		if($session === null || $target === null){
			$this->setLine(5, CollapseTranslationFactory::observe_scoreboard_status(TextFormat::RED . 'offline'));
			return;
		}
		$this->setLine(4, CollapseTranslationFactory::observe_scoreboard_ping((string) $target->getNetworkSession()->getPing()));
		$this->setLine(5, CollapseTranslationFactory::observe_scoreboard_status(TextFormat::GREEN . 'online'));
		$this->setLine(8, CollapseTranslationFactory::observe_scoreboard_input_name(InputModeUtils::toDisplayName($target->getProfile()->getInputMode())));
	}
}

<?php

declare(strict_types=1);

namespace collapse\game\duel\queue;

use collapse\game\duel\queue\entry\QueueEntry;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\player\scoreboard\CollapseScoreboard;
use collapse\PracticeConstants;
use collapse\resourcepack\Font;
use pocketmine\utils\TextFormat;
use function date;

final class QueueScoreboard extends CollapseScoreboard{

	public function __construct(
		CollapsePlayer $player,
		private readonly QueueEntry $entry
	){
		parent::__construct($player);
	}

	private function getFormattedTime() : string{
		return date('i:s', $this->entry->getWaitSeconds());
	}

	public function setUp() : void{
		$this->setLines([
			1 => TextFormat::GRAY . Font::SCOREBOARD_LINE,
			2 => CollapseTranslationFactory::queue_scoreboard_queue($this->getFormattedTime()),
			3 => '  ' . $this->entry->getQueue()->getMode()->toDisplayName(),
			4 => null,
			5 => ' ' . Font::bold(PracticeConstants::STORE_LINK),
			6 => TextFormat::BLACK . Font::SCOREBOARD_LINE,
		]);
	}

	public function onUpdate() : void{
		$this->setLine(2, CollapseTranslationFactory::queue_scoreboard_queue($this->getFormattedTime()));
	}
}

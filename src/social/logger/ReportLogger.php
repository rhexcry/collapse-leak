<?php

declare(strict_types=1);

namespace collapse\social\logger;

use collapse\report\Report;
use collapse\social\utils\MarkdownFormatter;
use function str_repeat;
use const EOL;

readonly class ReportLogger extends SocialLogger{

	public function onReport(Report $report): void{
		$message = MarkdownFormatter::toEscape(
			"📩 Жалоба от " .
			MarkdownFormatter::textToBold($report->getData()->getReporter()->getPlayerName()) .
			" на игрока " . MarkdownFormatter::textToBold($report->getData()->getTarget()->getPlayerName()) . ":" . str_repeat(EOL, 2) .
			MarkdownFormatter::textToItalic($report->getData()->getMessage()));

		$this->socialManager->getTelegram()->sendMessage(new TelegramReportMessage($message));
	}
}
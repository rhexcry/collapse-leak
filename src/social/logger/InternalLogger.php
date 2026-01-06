<?php

declare(strict_types=1);

namespace collapse\social\logger;

use collapse\Practice;
use collapse\system\internal\punish\PunishType;
use collapse\social\utils\MarkdownFormatter;
use const EOL;

final readonly class InternalLogger extends SocialLogger{

	public function onServerLag(float $lag) : void{
		$message = MarkdownFormatter::textToItalic('🚨 Замечен пролаг сервера ') . MarkdownFormatter::textToMonospace(round($lag, 2) . 's');
		$this->socialManager->getTelegram()->sendMessage(new TelegramInternalMessage($message));
		Practice::getInstance()->getLogger()->warning("Server lag detected: {$lag}s");
	}

	public function onIpBlock(string $ip, PunishType $type, ?string $reason = null, ?int $duration = null) : void{
		$reasonText = $reason ? MarkdownFormatter::textToMonospace($reason) : '';
		$durationText = '';
		if($duration !== null){
			$expiresAt = time() + $duration;
			$durationText = EOL . MarkdownFormatter::textToItalic('⏳ Истекает: ') . MarkdownFormatter::textToMonospace(date('Y-m-d H:i:s', $expiresAt));
		}
		$this->socialManager->getTelegram()->sendMessage(new TelegramInternalMessage(
			MarkdownFormatter::textToItalic('🛡️ Внутреннее наказание: ') . MarkdownFormatter::textToMonospace('IP BLOCK') . EOL .
			MarkdownFormatter::textToItalic('🌐 IP: ') . MarkdownFormatter::textToMonospace($ip) . EOL .
			MarkdownFormatter::textToItalic('📝 Тип: ') . MarkdownFormatter::textToMonospace($type->value) . EOL .
			MarkdownFormatter::textToItalic('📝 Причина: ') . $reasonText .
			$durationText
		));
		Practice::getInstance()->getLogger()->notice("Internal punishment: IP BLOCK | IP: {$ip} | Type: {$type->value} | Reason: {$reason} | Duration: " . ($duration ?? 'permanent'));
	}

	public function onIpUnblock(string $ip, string $reason) : void{
		$this->socialManager->getTelegram()->sendMessage(new TelegramInternalMessage(
			MarkdownFormatter::textToItalic('🔓 Разблокировка IP: ') . MarkdownFormatter::textToMonospace($ip) . EOL .
			MarkdownFormatter::textToItalic('📝 Причина: ') . MarkdownFormatter::textToMonospace($reason)
		));
		Practice::getInstance()->getLogger()->notice("Internal unblock: IP: {$ip} | Reason: {$reason}");
	}
}
<?php

declare(strict_types=1);

namespace collapse\social\logger;

use collapse\i18n\TranslatorLocales;
use collapse\player\CollapsePlayer;
use collapse\player\profile\Profile;
use collapse\punishments\Punishment;
use collapse\punishments\PunishmentType;
use collapse\punishments\rule\PunishmentRules;
use collapse\social\utils\MarkdownFormatter;
use collapse\wallet\currency\Currency;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use const EOL;

final readonly class StaffLogger extends SocialLogger{

	public function onPunishment(Punishment $punishment, Translatable $expires) : void{
		$translator = $this->socialManager->getPlugin()->getTranslatorManager()->fromLocale(TranslatorLocales::RUSSIAN);
		$reason = ($tr = PunishmentRules::getRule($punishment->getReason())?->getTranslation()) === null ? $punishment->getReason() : $translator->translate($tr);
		$this->socialManager->getTelegram()->sendMessage(new TelegramStaffMessage(
			MarkdownFormatter::textToItalic('👮‍♂️ Модератор: ') . MarkdownFormatter::textToMonospace($punishment->getSender()) . EOL .
			MarkdownFormatter::textToItalic('⚡ Действие: ') . MarkdownFormatter::textToMonospace($punishment->getType() === PunishmentType::Ban ? 'БАН' : 'МУТ') . EOL .
			MarkdownFormatter::textToItalic('👤 Игрок: ') . MarkdownFormatter::textToMonospace($punishment->getPlayerName()) . EOL .
			MarkdownFormatter::textToItalic('📝 Причина: ') . MarkdownFormatter::textToMonospace($reason) . EOL .
			MarkdownFormatter::textToItalic('⏳ Истекает: ') . MarkdownFormatter::textToMonospace($translator->translate($expires))
		));
	}

	public function onKick(CollapsePlayer $player, string $reason, CommandSender $sender) : void{
		$translator = $this->socialManager->getPlugin()->getTranslatorManager()->fromLocale(TranslatorLocales::RUSSIAN);
		$reason = ($tr = PunishmentRules::getRule($reason)?->getTranslation()) === null ? $reason : $translator->translate($tr);
		$this->socialManager->getTelegram()->sendMessage(new TelegramStaffMessage(
			MarkdownFormatter::textToItalic('👮‍♂️ Модератор: ') . MarkdownFormatter::textToMonospace($sender->getName()) . EOL .
			MarkdownFormatter::textToItalic('⚡ Действие: ') . MarkdownFormatter::textToMonospace('КИК') . EOL .
			MarkdownFormatter::textToItalic('📝 Причина: ') . MarkdownFormatter::textToMonospace($reason) . EOL .
			MarkdownFormatter::textToItalic('👤 Игрок: ') . MarkdownFormatter::textToMonospace($player->getName())
		));
	}

	public function onUnban(Punishment $punishment, CommandSender $sender) : void{
		$this->socialManager->getTelegram()->sendMessage(new TelegramStaffMessage(
			MarkdownFormatter::textToItalic('👮‍♂️ Модератор: ') . MarkdownFormatter::textToMonospace($sender->getName()) . EOL .
			MarkdownFormatter::textToItalic('⚡ Действие: ') . MarkdownFormatter::textToMonospace('РАЗБАН') . EOL .
			MarkdownFormatter::textToItalic('👤 Игрок: ') . MarkdownFormatter::textToMonospace($punishment->getPlayerName())
		));
	}

	public function onUnmute(Punishment $punishment) : void{
		$this->socialManager->getTelegram()->sendMessage(new TelegramStaffMessage(
			MarkdownFormatter::textToItalic('👮‍♂️ Модератор: ') . MarkdownFormatter::textToMonospace($punishment->getSender()) . EOL .
			MarkdownFormatter::textToItalic('⚡ Действие: ') . MarkdownFormatter::textToMonospace('РАЗМУТ') . EOL .
			MarkdownFormatter::textToItalic('👤 Игрок: ') . MarkdownFormatter::textToMonospace($punishment->getPlayerName())
		));
	}

	public function onCurrencyChange(CommandSender $sender, Profile $profile, Currency $currency, int|float $previous, int|float $balance) : void{
		$this->socialManager->getTelegram()->sendMessage(new TelegramStaffMessage(
			MarkdownFormatter::textToItalic('👮‍♂️ Администратор: ') . MarkdownFormatter::textToMonospace($sender->getName()) . EOL .
			MarkdownFormatter::textToItalic('👤 Игрок: ') . MarkdownFormatter::textToMonospace($profile->getPlayerName()) . EOL .
			MarkdownFormatter::textToItalic('⚡ Действие: ') . MarkdownFormatter::textToMonospace('ИЗМЕНЕНИЕ ВАЛЮТЫ') . EOL .
			MarkdownFormatter::textToItalic('⚡ Валюта: ') . MarkdownFormatter::textToMonospace($currency->getName()) . EOL .
			MarkdownFormatter::textToItalic('⚡ Баланс до: ') . MarkdownFormatter::textToMonospace((string) $previous) . EOL .
			MarkdownFormatter::textToItalic('⚡ Баланс после: ') . MarkdownFormatter::textToMonospace((string) $balance)
		));
	}

	public function onCheckAltsAccounts(CommandSender $sender, Profile $profile) : void{
		$this->socialManager->getTelegram()->sendMessage(new TelegramStaffMessage(
			MarkdownFormatter::textToItalic('👮‍♂️ Администратор: ') . MarkdownFormatter::textToMonospace($sender->getName()) . EOL .
			MarkdownFormatter::textToItalic('👤 Игрок: ') . MarkdownFormatter::textToMonospace($profile->getPlayerName()) . EOL .
			MarkdownFormatter::textToItalic('⚡ Действие: ') . MarkdownFormatter::textToMonospace('ПРОСМОТР АЛЬТ АККАУНТОВ')
		));
	}
}

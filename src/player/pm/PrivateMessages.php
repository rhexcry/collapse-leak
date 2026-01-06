<?php

declare(strict_types=1);

namespace collapse\player\pm;

use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\player\settings\Setting;
use collapse\Practice;
use collapse\punishments\rule\PunishmentRules;
use collapse\world\sound\MinecraftSound;
use collapse\world\sound\MinecraftSoundNames;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

final class PrivateMessages{

	private static array $reply = [];

	public static function isAnyoneSentFor(CollapsePlayer $player) : bool{
		return isset(PrivateMessages::$reply[$player->getName()]);
	}

	public static function getPlayerForReply(CollapsePlayer $player) : ?CollapsePlayer{
		if(!PrivateMessages::isAnyoneSentFor($player)){
			return null;
		}
		return Practice::getPlayerExact(PrivateMessages::$reply[$player->getName()]);
	}

	public static function send(CommandSender $sender, CollapsePlayer $player, string $message) : bool{
		if(!$player->getProfile()->getSetting(Setting::PrivateMessages) && $sender instanceof CollapsePlayer){
			$sender->sendTranslatedMessage(CollapseTranslationFactory::private_messages_disabled($player->getNameWithRankColor()));
			return false;
		}
		if($sender instanceof CollapsePlayer && ($punishment = $sender->getMutePunishment()) !== null){
			$sender->sendTranslatedMessage(CollapseTranslationFactory::chat_muted(
				PunishmentRules::getRule($punishment->getReason())?->getTranslation(true) ?? $punishment->getReason(),
				Practice::getInstance()->getPunishmentManager()->convertExpires($punishment)
			));
			return false;
		}

		$msg = TextFormat::clean($message);
		$player->sendTranslatedMessage(CollapseTranslationFactory::private_message_from(
			$sender instanceof CollapsePlayer ? $sender->getNameWithRankColor() : $sender->getName(),
			$msg
		), false);
		$player->getWorld()->addSound($player->getLocation(), new MinecraftSound(MinecraftSoundNames::RANDOM_ORB, 0.5, 1.05), [$player]);

		if($sender instanceof CollapsePlayer){
			$sender->sendTranslatedMessage(CollapseTranslationFactory::private_message_to(
				$player->getNameWithRankColor(),
				$msg
			), false);
			PrivateMessages::$reply[$player->getName()] = $sender->getName();
		}else{
			$sender->sendMessage(Practice::getInstance()->getTranslatorManager()->getDefaultTranslator()->translate(CollapseTranslationFactory::private_message_to(
				$player->getNameWithRankColor(),
				$msg
			)));
		}
		return true;
	}
}

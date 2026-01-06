<?php

declare(strict_types=1);

namespace collapse\player\chat;

use collapse\censor\Censor;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\player\rank\Rank;
use collapse\player\settings\Setting;
use collapse\Practice;
use collapse\resourcepack\Font;
use collapse\world\sound\MinecraftSound;
use collapse\world\sound\MinecraftSoundNames;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\utils\TextFormat;
use function microtime;
use function number_format;
use function str_replace;

final class ChatListener implements Listener{

	private const float DEFAULT_CHAT_COOLDOWN = 2.0;

	/** @var array<string, float> */
	private array $lastChatTimes = [];

	public function __construct(private readonly Censor $censorFilter){}

	/**
	 * @priority MONITOR
	 */
	public function handlePlayerChat(PlayerChatEvent $event) : void{
		if($event->isCancelled()){
			return;
		}
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		$message = $event->getMessage();

		if(preg_match_all('/@(\w+)/', $message, $matches)){
			$mentionedPlayers = $matches[1];

			foreach($mentionedPlayers as $mentionedName){
				$target = $player->getServer()->getPlayerExact($mentionedName);

				if($target instanceof CollapsePlayer && $target->isOnline()){
					if($target->getGame() !== null){
						$target->sendTranslatedPopup(CollapseTranslationFactory::mention_title());
						$target->getWorld()->addSound($target->getLocation(), new MinecraftSound(MinecraftSoundNames::NOTE_BASS), [$target]);
					}else{
						$target->sendTranslatedTitle(CollapseTranslationFactory::mention_title(), CollapseTranslationFactory::mention_subtitle(), 0, 20, 0);
					}
				}
			}
		}

		$currentTime = microtime(true);
		$lastChatTime = $this->lastChatTimes[$player->getXuid()] ?? 0;

		$cooldown = $player->getProfile()->getRank()->getPriority() >= Rank::BLAZING->getPriority()
			? self::DEFAULT_CHAT_COOLDOWN / 2
			: self::DEFAULT_CHAT_COOLDOWN;

		$timeLeft = $lastChatTime + $cooldown - $currentTime;

		if($timeLeft > 0 && $player->getProfile()->getRank() !== Rank::OWNER){
			if($player->getProfile()->getRank()->getPriority() >= Rank::BLAZING->getPriority()){
				$player->sendTranslatedMessage(CollapseTranslationFactory::chat_dont_spam(number_format($timeLeft, 2)));
			}else{
				$player->sendTranslatedMessage(CollapseTranslationFactory::chat_dont_spam_rank(number_format($timeLeft, 2), Rank::BLAZING->toFont()));
			}
			$event->cancel();
			return;
		}

		$this->lastChatTimes[$player->getXuid()] = $currentTime;

		$message = TextFormat::clean($event->getMessage());
		$profile = $player->getProfile();

		$format = TextFormat::colorize($profile->getRank()->toChatFormat());
		$chatTag = $profile->getChatTag() === null ? '' : ' ' . $profile->getChatTag()->toDisplayName();
		$formatted = str_replace(['{nickname}', '{message}'], [($name = $player->getProfile()->getRank() === Rank::DEFAULT ? $player->getName() : Font::text($player->getName())) . $chatTag, $message], $format);
		$formattedFiltered = str_replace(['{nickname}', '{message}'], [$name . $chatTag, $this->censorFilter->filter($message)], $format);

		foreach(Practice::onlinePlayers() as $viewer){
			if($viewer->getProfile()?->getSetting(Setting::FilterObsceneLexis)){
				$viewer->sendMessage($formattedFiltered, false);
			}else{
				$viewer->sendMessage($formatted, false);
			}
		}

		$player->getServer()->getLogger()->info(str_replace(
			['{nickname}', '{message}'],
			[$player->getName(), $message],
			TextFormat::colorize($player->getProfile()->getRank()->toConsoleChatFormat())
		));
		$event->cancel();
	}
}

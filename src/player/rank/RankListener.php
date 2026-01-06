<?php

declare(strict_types=1);

namespace collapse\player\rank;

use collapse\i18n\CollapseTranslationFactory;
use collapse\player\client\DeviceUtils;
use collapse\player\CollapsePlayer;
use collapse\player\profile\event\ProfileLoadedEvent;
use collapse\player\rank\event\ProfileRankChangeEvent;
use collapse\Practice;
use collapse\PracticeConstants;
use collapse\punishments\event\ProfilePrePunishEvent;
use collapse\resourcepack\Font;
use collapse\system\shop\event\PlayerPurchaseEvent;
use collapse\system\shop\types\ShopCategoryName;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use function str_contains;

final readonly class RankListener implements Listener{

	public function __construct(
		private RankManager $rankManager
	){
	}

	/**
	 * @priority LOWEST
	 */
	public function handleRankChange(ProfileRankChangeEvent $event) : void{
		$player = $event->getProfile()->getPlayer();
		if($player === null){
			return;
		}
		$player->getNetworkSession()->syncAvailableCommands();
		$this->rankManager->setPlayerNameTag($player);
	}

	/**
	 * @priority LOWEST
	 */
	public function handleProfileLoaded(ProfileLoadedEvent $event) : void{
		$profile = $event->getProfile();
		$player = $profile->getPlayer();
		foreach($profile->getRank()->getPermissions() as $permission){
			$player->addAttachment(Practice::getInstance(), $permission, true);
		}

		$playerName = $player->getProfile()->getRank() === Rank::DEFAULT ? $player->getNameWithRankColor() : Font::minecraftColorToUnicodeFont($player->getNameWithRankColor());
		$player->setNameTag(DeviceUtils::toFont($profile->getDeviceOS()) . ' ' . $playerName);

		$msg = PracticeConstants::PLAYER_JOIN_MESSAGE . $playerName;
		foreach(Practice::onlinePlayers() as $player){
			$player->sendMessage($msg, false);
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerQuit(PlayerQuitEvent $event) : void{
		/** @var CollapsePlayer $player */
		$player = $event->getPlayer();
		if($player->getProfile() === null){
			return;
		}

		$playerName = $player->getProfile()->getRank() === Rank::DEFAULT ? $player->getNameWithRankColor() : Font::minecraftColorToUnicodeFont($player->getNameWithRankColor());
		$msg = PracticeConstants::PLAYER_QUIT_MESSAGE . $playerName;
		foreach(Practice::onlinePlayers() as $player){
			$player->sendMessage($msg, false);
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handleProfilePrePunish(ProfilePrePunishEvent $event) : void{
		$profile = $event->getProfile();
		$sender = $event->getSender();
		if($sender instanceof CollapsePlayer && $profile->getRank()->getPriority() > $sender->getProfile()->getRank()->getPriority()){
			$event->cancel();
			$sender->sendTranslatedMessage(CollapseTranslationFactory::you_cant_punish_player());
		}
	}

	/**
	 * @priority LOWEST
	 */
	public function handlePlayerPurchase(PlayerPurchaseEvent $event) : void{
		$item = $event->getShopItem();
		if(str_contains($item->getCategory()->getId(), ShopCategoryName::Ranks->value)){
			$player = $event->getProfile()->getPlayer();
			if($player === null){
				$event->getProfile()->setRank(Rank::from($item->getId()));
				return;
			}

			$player->getProfile()->setRank(Rank::from($item->getId()));
		}
	}
}

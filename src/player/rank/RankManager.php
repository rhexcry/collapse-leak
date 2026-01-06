<?php

declare(strict_types=1);

namespace collapse\player\rank;

use collapse\player\client\DeviceUtils;
use collapse\player\CollapsePlayer;
use collapse\player\profile\Profile;
use collapse\player\rank\command\SetRankCommand;
use collapse\player\rank\event\ProfileRankChangeEvent;
use collapse\Practice;
use collapse\resourcepack\Font;
use function array_keys;
use function array_map;

final readonly class RankManager{

	public function __construct(private Practice $plugin){
		$this->plugin->getServer()->getPluginManager()->registerEvents(new RankListener($this), $this->plugin);
		$this->plugin->getServer()->getCommandMap()->register('collapse', new SetRankCommand($this));
	}

	public function setRank(Profile $profile, Rank $rank) : void{
		$profile->setRank($rank);
		(new ProfileRankChangeEvent($profile, $rank))->call();
		$profile->save();
	}

	public function setPlayerNameTag(CollapsePlayer $player) : void{
		$playerName = $player->getProfile()->getRank() === Rank::DEFAULT ? $player->getNameWithRankColor() : Font::minecraftColorToUnicodeFont($player->getNameWithRankColor());
		$player->setNameTag(DeviceUtils::toFont($player->getProfile()->getDeviceOS()) . ' ' . $playerName);
	}

	/**
	 * Players array should have XUID as key. Returns player's object or player colored name
	 *
	 * @param (CollapsePlayer|null)[] $players
	 * @return string[]
	 */
	public function prepareToNamesWithColors(array $players) : array{
		return array_map(function(?CollapsePlayer $player, string $xuid) : CollapsePlayer|string{
			if($player === null){
				$profile = $this->plugin->getProfileManager()->getProfileByXuid($xuid);
				return $profile->getRank()->toColor() . $profile->getPlayerName();
			}
			return $player;
		}, $players, array_keys($players));
	}
}

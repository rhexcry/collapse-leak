<?php

declare(strict_types=1);

namespace collapse\system\moderatorpoints;

use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\system\moderatorpoints\command\ModeratorPointsCommand;

final class ModeratorPointsManager{

	private const int MP_TASK_INTERVAL = 20 * 60; // every minute
	private const int AFK_KICK_TIME = 300; // 5 minutes in seconds

	private const int MP_BAN = 4;
	private const int MP_MUTE = 2;
	private const int MP_KICK = 3;
	private const int MP_ONLINE_MINUTE = 2;

	/** @var array<string, int> */
	private array $lastMovement = [];

	public function __construct(private readonly Practice $plugin){
		$this->plugin->getServer()->getPluginManager()->registerEvents(new ModeratorPointsListener($this), $this->plugin);
		$this->plugin->getScheduler()->scheduleRepeatingTask(new ModeratorPointsTask($this), self::MP_TASK_INTERVAL);
		$this->plugin->getServer()->getCommandMap()->register('collapse', new ModeratorPointsCommand($this));
	}

	public function addMpForPunishment(CollapsePlayer $issuer, string $type) : void{
		$profile = $issuer->getProfile();
		if($profile === null || !$profile->getRank()->isStaffRank()){
			return;
		}
		$amount = match($type){
			'ban' => self::MP_BAN,
			'mute' => self::MP_MUTE,
			'kick' => self::MP_KICK,
			default => 0
		};
		if($amount > 0){
			$profile->addIssuedPunishment($type);
			$profile->addMp($amount);
			$profile->save();
		}
	}

	public function addMpForOnlineMinutes() : void{
		foreach(Practice::onlinePlayers() as $player){
			if($player->getProfile() !== null && $player->getProfile()->getRank()->isModeratorRank()){
				$player->getProfile()->addOnlineMinute();
				$player->getProfile()->addMp(self::MP_ONLINE_MINUTE);
				$player->getProfile()->save();
			}
		}
	}

	public function checkAFK() : void{
		foreach(Practice::onlinePlayers() as $player){
			if($player->getProfile()?->getRank()->isModeratorRank()){
				$xuid = $player->getXuid();
				$lastMove = $this->lastMovement[$xuid] ?? null;
				if($lastMove === null){
					$lastMove = time();
					$this->lastMovement[$xuid] = $lastMove;
				}

				if(time() - $lastMove > self::AFK_KICK_TIME){
					$player->kick('AFK for 5 minutes');
					unset($this->lastMovement[$xuid]);
				}
			}
		}
	}

	public function setLastMovement(CollapsePlayer $player) : void{
		$this->lastMovement[$player->getXuid()] = time();
	}
}
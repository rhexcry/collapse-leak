<?php

declare(strict_types=1);

namespace collapse\game\ffa;

use collapse\cosmetics\effects\death\DeadBodyDeathEffect;
use collapse\game\ffa\item\FreeForAllItems;
use collapse\game\ffa\modes\build\Build;
use collapse\game\ffa\modes\crystal\Crystal;
use collapse\game\ffa\modes\midfight\MidFight;
use collapse\game\ffa\modes\sumo\Sumo;
use collapse\game\ffa\scoreboard\FreeForAllScoreboard;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\player\settings\Setting;
use collapse\resourcepack\Font;
use collapse\world\sound\MinecraftSound;
use collapse\world\sound\MinecraftSoundNames;
use pocketmine\lang\Translatable;
use pocketmine\player\GameMode;

final class FreeForAllPlayerManager{

	private const int DEFAULT_RESPAWN_COUNTDOWN = 2;

	/** @var CollapsePlayer[] */
	private array $players = [];

	public function __construct(
		private readonly FreeForAllArena $arena
	){}

	public function getPlayers() : array{
		return $this->players;
	}

	public function hasPlayer(CollapsePlayer $player) : bool{
		return isset($this->players[$player->getName()]);
	}

	public function addPlayer(CollapsePlayer $player) : void{
		$this->players[$player->getName()] = $player;
		$player->setGame($this->arena);
		$player->setKnockBack($this->arena->getKnockBack());
		$player->setScoreboard(new FreeForAllScoreboard($player));
		$player->sendTranslatedMessage(CollapseTranslationFactory::free_for_all_joined(Font::minecraftColorToUnicodeFont($this->arena->getConfig()->getMode()->toDisplayName())));
		$this->onPlayerSpawn($player);
	}

	public function onPlayerLeave(CollapsePlayer $player) : void{
		$this->arena->getRespawnManager()->cancelRespawn($player);

		if(($opponent = $this->arena->getOpponentManager()?->getOpponent($player)) !== null){
			$this->onPlayerKill($player, $opponent, false);
		}
		if($this->arena instanceof Crystal || $this->arena instanceof Build){
			$this->arena->getBlockManager()->onPlayerDie($player);
		}
		$this->removePlayer($player);
	}

	public function onPlayerSpawn(CollapsePlayer $player) : void{
		$this->arena->getOpponentManager()?->onPlayerSpawn($player);
		$spawnLocation = $this->arena->getConfig()->getSpawnLocation();
		if($this->arena->hasRandomSpawn()){
			$spawnLocation = $this->arena->getRandomSpawn();
		}
		$player->teleport($spawnLocation);
		$this->reset($player);
	}

	public function onPlayerRespawn(CollapsePlayer $player) : void{
		$arena = $player->getGame();
		if(!$arena instanceof FreeForAllArena){
			return;
		}

		if($player->getProfile()->getSetting(Setting::AutoRespawn)){
			$spawnLocation = $this->arena->getConfig()->getSpawnLocation();
			if($this->arena->hasRandomSpawn()){
				$spawnLocation = $this->arena->getRandomSpawn();
			}
			$player->teleport($spawnLocation);
			$this->reset($player);
			$player->getWorld()->addSound($player->getLocation(), new MinecraftSound(MinecraftSoundNames::NOTE_HAT, 0.5), [$player]);
			return;
		}

		$player->getWorld()->addSound($player->getLocation(), new MinecraftSound(MinecraftSoundNames::NOTE_HAT, 0.5), [$player]);

		$player->getInventory()->setItem(1, FreeForAllItems::RESPAWN()->translate($player));
		$player->getInventory()->setItem(7, FreeForAllItems::LOBBY()->translate($player));
	}

	public function removePlayer(CollapsePlayer $player) : void{
		unset($this->players[$player->getName()]);
		if($player->getGame() === $this->arena){
			$player->setGame(null);
			$player->setKnockBack(null);
		}
		$player->getScoreboard()?->remove();
	}

	public function reset(CollapsePlayer $player) : void{
		$this->arena->getPlugin()->getCooldownManager()->cancelAll($player);
		$player->setBasicProperties($this->arena->isBlocksActions() ? GameMode::SURVIVAL : GameMode::ADVENTURE);

		$kit = $this->arena->getConfig()->getMode()->toKit();
		$profile = $player->getProfile();
		$layout = $profile->getKitLayout($kit);
		if($layout === null){
			$this->arena->getKit()->applyTo($player);
		}else{
			$this->arena->getPlugin()->getKitEditorManager()->equipLayoutOnKit($layout, $kit)->applyTo($player);
		}
	}

	public function onPlayerDie(CollapsePlayer $player) : void{
		if(!($this->arena instanceof Sumo) && !($this->arena instanceof MidFight)){
			$this->arena->getOpponentManager()?->removeFromCombat($player);
		}
		$player->setBasicProperties(GameMode::SPECTATOR);
		$this->arena->getRespawnManager()->respawn($player, self::DEFAULT_RESPAWN_COUNTDOWN);
	}

	public function onPlayerKill(CollapsePlayer $player, CollapsePlayer $killer, bool $respawn = true) : void{
		if($respawn){
			$player->setBasicProperties(GameMode::SPECTATOR);
			$this->arena->getRespawnManager()->respawn($player, self::DEFAULT_RESPAWN_COUNTDOWN);
		}
		if(!$killer->isConnected()){
			$this->arena->getOpponentManager()?->removeFromCombat($player);
			return;
		}

		$player->getProfile()->getDeathEffect()?->create($player, $killer);

		$this->arena->getOpponentManager()?->removeFromCombat($player);
		$this->reset($killer);

		$killer->getProfile()->onFreeForAllKill($this->arena->getConfig()->getMode());
		$player->getProfile()->onFreeForAllDeath($this->arena->getConfig()->getMode());

		new DeadBodyDeathEffect($player, $killer);
	}

	public function broadcastMessage(Translatable $translation, bool $prefix = true) : void{
		foreach($this->players as $player){
			if($player->isConnected()){
				$player->sendTranslatedMessage($translation, $prefix);
			}
		}
	}
}

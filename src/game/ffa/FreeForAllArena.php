<?php

declare(strict_types=1);

namespace collapse\game\ffa;

use collapse\game\event\PlayerJoinGameEvent;
use collapse\game\Game;
use collapse\game\kb\KnockBack;
use collapse\game\kit\KitCollection;
use collapse\game\kit\Kits;
use collapse\game\respawn\GameRespawnManager;
use collapse\game\statistics\GameStatisticsManager;
use collapse\player\CollapsePlayer;
use collapse\player\rank\Rank;
use collapse\Practice;
use collapse\resourcepack\Font;
use pocketmine\utils\TextFormat;
use function array_map;
use function ceil;
use function implode;

abstract class FreeForAllArena implements Game{

	public const int RESPAWN_COUNTDOWN = 2;

	protected readonly KitCollection $kit;

	protected readonly KnockBack $knockBack;

	private readonly FreeForAllPlayerManager $playerManager;

	private readonly GameRespawnManager $respawnManager;

	private readonly ?FreeForAllOpponentManager $opponentManager;

	public function __construct(
		protected readonly Practice $plugin,
		protected readonly FreeForAllConfig $config
	){
		$this->kit = Kits::get($this->config->getMode()->toKit());
		$this->knockBack = FreeForAllKnockBacks::get($this->config->getMode());
		$this->playerManager = new FreeForAllPlayerManager($this);
		$this->respawnManager = new GameRespawnManager($this, self::RESPAWN_COUNTDOWN);
		$this->opponentManager = ($this->isAntiInterrupt() || $this->isCombat()) ? new FreeForAllOpponentManager($this) : null;
		$this->setUp();
	}

	public function getPlugin() : Practice{
		return $this->plugin;
	}

	final public function getConfig() : FreeForAllConfig{
		return $this->config;
	}

	final public function getPlayerManager() : FreeForAllPlayerManager{
		return $this->playerManager;
	}

	final public function getRespawnManager() : GameRespawnManager{
		return $this->respawnManager;
	}

	final public function getOpponentManager() : ?FreeForAllOpponentManager{
		return $this->opponentManager;
	}

	final public function getKit() : KitCollection{
		return $this->kit;
	}

	final public function getKnockBack() : KnockBack{
		return $this->knockBack;
	}

	protected function setUp() : void{}

	/**
	 * @param CollapsePlayer[] $players
	 */
	final public function createStatistics(array $players) : GameStatisticsManager{
		$statisticsManager = GameStatisticsManager::simple($this);

		$this->kit->addAdditionalStatistics($statisticsManager, $players);
		return $statisticsManager;
	}

	public function getStatistics(?CollapsePlayer $player = null) : ?GameStatisticsManager{
		return $this->opponentManager?->getStatistics($player);
	}

	private function formatTags(array $tags) : string{
		return implode(' ', array_map(static function(string $tag) : string{
			return TextFormat::GRAY . '[' . $tag . TextFormat::GRAY . ']';
		}, $tags));
	}

	final public function createKillerBroadcastTags(CollapsePlayer $player, ?GameStatisticsManager $manager) : string{
		$tags = $this->addAdditionalKillerTags($player, $manager);
		if(!$this->isDamageDisabled()){
			$tags[] = TextFormat::RED . ceil($player->getHealth()) . ' HP';
		}
		if(empty($tags)){
			return $player->getProfile()->getRank() === Rank::DEFAULT ? $player->getNameWithRankColor() : Font::minecraftColorToUnicodeFont($player->getNameWithRankColor());
		}
		return ($player->getProfile()->getRank() === Rank::DEFAULT ? $player->getNameWithRankColor() : Font::minecraftColorToUnicodeFont($player->getNameWithRankColor())) . ' ' . $this->formatTags($tags);
	}

	protected function addAdditionalKillerTags(CollapsePlayer $player, ?GameStatisticsManager $manager) : array{
		return [];
	}

	final public function createPlayerBroadcastTags(CollapsePlayer $player, ?GameStatisticsManager $manager) : string{
		$tags = $this->addAdditionalPlayerTags($player, $manager);
		if(empty($tags)){
			return $player->getProfile()->getRank() === Rank::DEFAULT ? $player->getNameWithRankColor() : Font::minecraftColorToUnicodeFont($player->getNameWithRankColor());
		}
		return ($player->getProfile()->getRank() === Rank::DEFAULT ? $player->getNameWithRankColor() : Font::minecraftColorToUnicodeFont($player->getNameWithRankColor())) . ' ' . $this->formatTags($tags);
	}

	protected function addAdditionalPlayerTags(CollapsePlayer $player, ?GameStatisticsManager $manager) : array{
		return [];
	}

	public function isBlocksActions() : bool{
		return false;
	}

	public function isAntiInterrupt() : bool{
		return true;
	}

	public function onPlayerJoin(CollapsePlayer $player, \Closure $callback) : void{
		$event = new PlayerJoinGameEvent($this, $player);
		$event->call();
		if($event->isCancelled()){
			return;
		}
		$callback($this);
	}

	public function onPlayerLeave(CollapsePlayer $player) : void{
		$this->playerManager->onPlayerLeave($player);
	}

	public function removePlayer(CollapsePlayer $player) : void{
		$this->playerManager->removePlayer($player);
	}

	public function setDamageY(int $damageY) : void{
		$world = $this->getConfig()->getSpawnLocation()->getWorld();

		$reflection = new \ReflectionClass($world);
		$property = $reflection->getProperty('damageY');
		$property->setAccessible(true);
		$property->setValue($world, $damageY);
	}

	public function isDamageDisabled() : bool{
		return false;
	}

	public function isFallDamageDisabled() : bool{
		return true;
	}

	public function isEnderPearlCooldown() : bool{
		return false;
	}

	public function isHidePlayersInCombat() : bool{
		return true;
	}

	public function isCombat() : bool{
		return false;
	}

	public function isStatisticsEnabled() : bool{
		return true;
	}

	public function hasRandomSpawn() : bool{
		return false;
	}
}

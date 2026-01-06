<?php

declare(strict_types=1);

namespace collapse\lobby;

use collapse\game\event\PlayerKillPlayerGameEvent;
use collapse\game\ffa\FreeForAllArena;
use collapse\i18n\CollapseTranslationFactory;
use collapse\lobby\command\LobbyCommand;
use collapse\lobby\npc\BuildFreeForAll;
use collapse\lobby\npc\Duels;
use collapse\lobby\npc\FireballFightDuels;
use collapse\lobby\npc\FreeForAll;
use collapse\npc\location\DefaultNPCLocationDefinitions;
use collapse\player\CollapsePlayer;
use collapse\player\rank\Rank;
use collapse\Practice;
use collapse\skin\geometry\SkinGeometry;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\player\GameMode;
use pocketmine\utils\Config;
use pocketmine\world\World;
use Symfony\Component\Filesystem\Path;
use function is_dir;
use function max;
use function min;
use function mkdir;

final class LobbyManager{

	private const string LOBBY_SETTINGS_FILE_PATH = 'lobby';

	/** @var CollapsePlayer[] */
	private array $players = [];

	/** @var CollapsePlayer[] */
	private array $hiddenPlayers = [];

	private Location $spawnLocation;

	public function __construct(private readonly Practice $plugin){
		if(!is_dir(Path::join($this->plugin->getDataFolder(), self::LOBBY_SETTINGS_FILE_PATH))){
			mkdir(Path::join($this->plugin->getDataFolder(), self::LOBBY_SETTINGS_FILE_PATH), 0775, true);
		}

		LobbyItems::init();

		$world = $this->plugin->getServer()->getWorldManager()->getDefaultWorld();
		$world->setTime(World::TIME_DAY);
		$world->stopTime();

		$settingsFilePath = Path::join($this->plugin->getDataFolder(), self::LOBBY_SETTINGS_FILE_PATH, 'settings.json');
		$this->plugin->saveResource(Path::join(self::LOBBY_SETTINGS_FILE_PATH, 'settings.json'));
		$settings = (new Config($settingsFilePath, Config::JSON))->getAll();
		$this->spawnLocation = new Location(
			$settings['spawn']['x'],
			$settings['spawn']['y'],
			$settings['spawn']['z'],
			$world,
			$settings['spawn']['yaw'],
			$settings['spawn']['pitch'],
		);

		$this->plugin->getScheduler()->scheduleRepeatingTask(new LobbyVisibilityTask($this), 20);
		$this->plugin->getScheduler()->scheduleRepeatingTask(new LobbyAntiFallTask($this), 20);
		$this->plugin->getServer()->getPluginManager()->registerEvents(new LobbyListener($this), $this->plugin);
		$this->plugin->getServer()->getCommandMap()->register('collapse', new LobbyCommand($this));

		//TODO: перетащи говно в конфиг, перестань хардкодить
		$min = new Vector3(-15, -40, -5);
		$max = new Vector3(-10, -35, 5);

		$this->plugin->getAreaManager()->add(new LobbyBoostPad(
			new AxisAlignedBB(
				min($min->x, $max->x),
				min($min->y, $max->y),
				min($min->z, $max->z),
				max($min->x, $max->x),
				max($min->y, $max->y),
				max($min->z, $max->z)
			),
			$world
		));

		$skinFactory = $this->plugin->getSkinFactory();
		$npcManager = $this->plugin->getNpcManager();

		$npcManager->add(new FireballFightDuels(
			DefaultNPCLocationDefinitions::Duels_FireballFight->getLocation(),
			$skinFactory->withGeometry(
				$skinFactory->loadFromFile('npc/duels/fireball_fight.png'),
				new SkinGeometry('geometry.fireball', 'fireball_fight.geo.json')
			)));

		$npcManager->add(new BuildFreeForAll(
			DefaultNPCLocationDefinitions::FFA_Build->getLocation(),
			$skinFactory->withGeometry(
				$skinFactory->loadFromFile('npc/ffa/build.png'),
				new SkinGeometry('geometry.build', 'build.geo.json')
			)));

		$npcManager->add(new Duels(
			DefaultNPCLocationDefinitions::Duels->getLocation(),
			$skinFactory->withGeometry(
				$skinFactory->loadFromFile('npc/duels/menu.png'),
				new SkinGeometry('geometry.duels', 'duels.geo.json')
			)));

		$npcManager->add((new FreeForAll(
			DefaultNPCLocationDefinitions::FFA->getLocation(),
			$skinFactory->withGeometry(
				$skinFactory->loadFromFile('npc/ffa/menu.png'),
				new SkinGeometry('geometry.ffa', 'ffa.geo.json')))));

		/*$npcManager->add(new Rules(
			DefaultNPCLocationDefinitions::Collapse->getLocation(),
			$skinFactory->withGeometry(
				$skinFactory->loadFromFile('npc/rules.png'),
				new SkinGeometry('geometry.rules', 'rules.geo.json')
			)
		));*/
	}

	public function getPlugin() : Practice{
		return $this->plugin;
	}

	public function getPlayers() : array{
		return $this->players;
	}

	public function getSpawnLocation() : Location{
		return $this->spawnLocation;
	}

	public function isInLobby(CollapsePlayer $player) : bool{
		return isset($this->players[$player->getName()]);
	}

	public function isHiddenFromPlayers(CollapsePlayer $player) : bool{
		return isset($this->hiddenPlayers[$player->getName()]);
	}

	public function hideFromPlayers(CollapsePlayer $player) : void{
		if($this->isHiddenFromPlayers($player)){
			return;
		}
		foreach($this->players as $lobbyPlayer){
			$lobbyPlayer->hidePlayer($player);
		}
		$this->hiddenPlayers[$player->getName()] = true;
	}

	public function showToPlayers(CollapsePlayer $player) : void{
		if(!$this->isHiddenFromPlayers($player)){
			return;
		}
		foreach($this->players as $lobbyPlayer){
			$lobbyWorld = $lobbyPlayer->getPosition()->world;
			$world = $player->getPosition()->world;
			if($lobbyWorld == null || !$lobbyWorld->isLoaded() || $world == null || !$world->isLoaded()){
				continue;
			}
			$lobbyPlayer->showPlayer($player);
		}
		unset($this->hiddenPlayers[$player->getName()]);
	}

	public function sendToLobby(CollapsePlayer $player) : void{
		if(($game = $player->getGame()) !== null){
			if($game instanceof FreeForAllArena){
				$opponentManager = $game->getOpponentManager();

				if($opponentManager === null || ($opponnent = $opponentManager->getOpponent($player)) === null){
					$game->onPlayerLeave($player);
					$this->internalSendToLobby($player);
					return;
				}

				$ev = new PlayerKillPlayerGameEvent($game, $player, $opponnent, EntityDamageEvent::CAUSE_ENTITY_ATTACK);
				$ev->call();
			}

			$player->setGame(null);
			$game->onPlayerLeave($player);
		}

		$this->internalSendToLobby($player);
	}

	public function internalSendToLobby(CollapsePlayer $player) : void{
		$observerManager = $this->plugin->getObserveManager();
		$session = $observerManager->getSession($player);
		if($session !== null){
			$observerManager->stopObserving($player);
			$player->sendTranslatedMessage(CollapseTranslationFactory::command_observe_stop($session->getTarget()->getNameWithRankColor()));
		}

		$this->plugin->getCooldownManager()->cancelAll($player);

		$this->players[$player->getName()] = $player;
		$player->teleport($this->spawnLocation);
		$player->setBasicProperties(GameMode::ADVENTURE);
		$this->setProperties($player);
		//$this->hideFromPlayers($player);
		if($player->getProfile()->getRank() !== Rank::DEFAULT){
			$player->setAllowFlight(true);
		}
		$this->plugin->getCosmeticsManager()->getChatTagsManager()->setChatTag($player->getProfile(), null);
	}

	public function setProperties(CollapsePlayer $player) : void{
		$player->setScoreboard(new LobbyScoreboard($player));
		$inventory = $player->getInventory();
		$inventory->setItem(0, LobbyItems::DUELS()->translate($player));
		$inventory->setItem(1, LobbyItems::FREE_FOR_ALL()->translate($player));
		$inventory->setItem(3, LobbyItems::SHOP()->translate($player));
		$inventory->setItem(4, LobbyItems::COSMETICS()->translate($player));
		$inventory->setItem(6, LobbyItems::LEADERBOARDS()->translate($player));
		$inventory->setItem(7, LobbyItems::QUESTS()->translate($player));
		$inventory->setItem(8, LobbyItems::PROFILE()->translate($player));
	}

	public function removeFromLobby(CollapsePlayer $player) : void{
		$this->showToPlayers($player);
		$player->setScoreTag('');
		unset($this->players[$player->getName()]);
	}
}

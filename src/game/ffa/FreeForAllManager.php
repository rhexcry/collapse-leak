<?php

declare(strict_types=1);

namespace collapse\game\ffa;

use collapse\game\ffa\command\ReKitCommand;
use collapse\game\ffa\item\FreeForAllItems;
use collapse\game\ffa\listener\FreeForAllListener;
use collapse\game\ffa\tasks\FreeForAllCombatUpdateTask;
use collapse\game\ffa\types\FreeForAllMode;
use collapse\Practice;
use pocketmine\entity\Location;
use pocketmine\utils\Config;
use pocketmine\world\World;
use function array_filter;
use function count;

final class FreeForAllManager{

	private const string CONFIG_FILE_PATH = 'ffa/ffa_arenas.json';

	/** @var array<string, FreeForAllArena> */
	private array $arenaMap = [];

	public function __construct(private readonly Practice $plugin){
		$this->plugin->saveResource(self::CONFIG_FILE_PATH);
		$definitions = new Config($this->plugin->getDataFolder() . self::CONFIG_FILE_PATH, Config::JSON);
		$worldManager = $this->plugin->getServer()->getWorldManager();
		foreach($definitions->getAll() as $name => $data){
			$mode = FreeForAllMode::from($data[FreeForAllConfigKeys::MODE]);
			$spawnLocation = $data[FreeForAllConfigKeys::SPAWN_LOCATION];
			$worldName = $spawnLocation[FreeForAllConfigKeys::SPAWN_LOCATION_WORLD];
			$worldManager->loadWorld($worldName);
			$world = $worldManager->getWorldByName($worldName);
			if($world === null){
				throw new \RuntimeException('World ' . $worldName . ' not found');
			}
			$world->setAutoSave(false);
			$world->setTime(World::TIME_NOON);
			$world->stopTime();
			$world->setDifficulty(World::DIFFICULTY_HARD);
			$config = new FreeForAllConfig(
				$mode,
				new Location(
					$spawnLocation[FreeForAllConfigKeys::SPAWN_LOCATION_X],
					$spawnLocation[FreeForAllConfigKeys::SPAWN_LOCATION_Y],
					$spawnLocation[FreeForAllConfigKeys::SPAWN_LOCATION_Z],
					$world,
					$spawnLocation[FreeForAllConfigKeys::SPAWN_LOCATION_YAW] ?? 0.0,
					$spawnLocation[FreeForAllConfigKeys::SPAWN_LOCATION_PITCH] ?? 0.0,
				),
				$data
			);
			$this->registerArena($name, $mode->create($this->plugin, $config));
		}

		FreeForAllItems::init();
		$this->plugin->getServer()->getPluginManager()->registerEvents(new FreeForAllListener($this), $this->plugin);
		$this->plugin->getScheduler()->scheduleRepeatingTask(new FreeForAllCombatUpdateTask($this), 20);
		$this->plugin->getServer()->getCommandMap()->register('collapse', new ReKitCommand());
	}

	public function getPlugin() : Practice{
		return $this->plugin;
	}

	public function getPlaying(?FreeForAllMode $mode = null) : int{
		$playing = 0;
		foreach(array_filter($this->arenaMap, static function(FreeForAllArena $freeForAll) use ($mode) : bool{
			if($mode !== null && $freeForAll->getConfig()->getMode() !== $mode){
				return false;
			}
			return true;
		}) as $freeForAll){
			$playing += count($freeForAll->getPlayerManager()->getPlayers());
		}
		return $playing;
	}

	private function registerArena(string $name, FreeForAllArena $arena) : void{
		if(isset($this->arenaMap[$name])){
			throw new \RuntimeException('Arena with name "' . $name . '" already registered');
		}
		$this->arenaMap[$name] = $arena;
	}

	public function getArena(FreeForAllMode $mode) : FreeForAllArena{
		foreach($this->arenaMap as $arena){
			if($arena->getConfig()->getMode() === $mode){
				return $arena;
			}
		}

		throw new \RuntimeException('Arena with mode "' . $mode->value . '" not found');
	}

	public function getArenas() : array{
		return $this->arenaMap;
	}
}

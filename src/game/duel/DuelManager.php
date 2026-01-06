<?php

declare(strict_types=1);

namespace collapse\game\duel;

use collapse\game\duel\command\DuelCommand;
use collapse\game\duel\command\NoCommand;
use collapse\game\duel\command\SpectateCommand;
use collapse\game\duel\command\YesCommand;
use collapse\game\duel\listener\DuelListener;
use collapse\game\duel\promise\DuelCreationPromise;
use collapse\game\duel\queue\QueueManager;
use collapse\game\duel\ranked\EloCalculator;
use collapse\game\duel\records\DuelRecordManager;
use collapse\game\duel\requests\DuelRequestManager;
use collapse\game\duel\tasks\DuelsUpdateTask;
use collapse\game\duel\types\DuelMode;
use collapse\game\duel\types\DuelType;
use collapse\game\teams\TeamManager;
use collapse\Practice;
use collapse\world\task\WorldCopyAsyncTask;
use Symfony\Component\Filesystem\Path;
use function array_filter;
use function count;

final class DuelManager{

	public const string DUEL_SETTINGS_FILE_PATH = 'duels';

	private const string DUEL_WORLD_PREFIX = '.duel';

	private static int $duelNextId = 0;

	private static function nextDuelId() : int{
		return self::$duelNextId++;
	}

	private DuelMapPool $mapPool;

	private QueueManager $queueManager;

	private DuelRequestManager $requestManager;

	private DuelRecordManager $recordManager;

	private EloCalculator $eloCalculator;

	/** @var Duel[] */
	private array $duels = [];

	public function __construct(
		private readonly Practice $plugin
	){
		$this->mapPool = new DuelMapPool($this->plugin);
		$this->queueManager = new QueueManager($this);
		$this->requestManager = new DuelRequestManager($this);
		$this->recordManager = new DuelRecordManager($this);
		$this->eloCalculator = new EloCalculator(35, 7, 25, 7, 25);
		$this->plugin->getServer()->getCommandMap()->registerAll('collapse', [
			new DuelCommand($this),
			new SpectateCommand($this),
			new YesCommand($this),
			new NoCommand($this)
		]);
		$this->plugin->getScheduler()->scheduleRepeatingTask(new DuelsUpdateTask($this), 20);
		$this->plugin->getServer()->getPluginManager()->registerEvents(new DuelListener($this), $this->plugin);
	}

	public function getPlugin() : Practice{
		return $this->plugin;
	}

	public function getMapPool() : DuelMapPool{
		return $this->mapPool;
	}

	public function getQueueManager() : QueueManager{
		return $this->queueManager;
	}

	public function getRequestManager() : DuelRequestManager{
		return $this->requestManager;
	}

	public function getRecordManager() : DuelRecordManager{
		return $this->recordManager;
	}

	public function getEloCalculator() : EloCalculator{
		return $this->eloCalculator;
	}

	public function getDuels() : array{
		return $this->duels;
	}

	public function getPlaying(?DuelType $type = null, ?DuelMode $mode = null) : int{
		$playing = 0;
		foreach(array_filter($this->duels, static function(Duel $duel) use ($type, $mode) : bool{
			if($type !== null && $duel->getType() !== $type){
				return false;
			}
			if($mode !== null && $duel->getConfig()->getMode() !== $mode){
				return false;
			}
			return true;
		}) as $duel){
			$playing += count($duel->getPlayerManager()->getPlayers());
		}
		return $playing;
	}

	public function add(DuelConfig $config, DuelType $type) : DuelCreationPromise{
		$promise = new DuelCreationPromise();
		$id = self::nextDuelId();
		$this->plugin->getServer()->getAsyncPool()->submitTask(new WorldCopyAsyncTask(
			$config->getMapPath(),
			Path::join($this->plugin->getServer()->getDataPath(), 'worlds', $world = self::DUEL_WORLD_PREFIX . $id),
			function() use ($config, $type, $promise, $id, $world) : void{
				$worldManager = new DuelWorldManager($this->plugin, $world);
				$teamManager = new TeamManager(($type === DuelType::PartyRequest ? 2 : 1), count($config->getSpawnLocations()));
				$duel = $config->getMode()->create(
					$id,
					$this->plugin,
					$config,
					$type,
					$worldManager,
					$teamManager
				);
				$this->duels[$id] = $duel;
				$promise->resolve($duel);
			}
		));
		return $promise;
	}

	public function closeDuel(Duel $duel, bool $force = false) : void{
		if(!isset($this->duels[$duel->getId()])){
			return;
		}

		$duel->close();
		unset($this->duels[$duel->getId()]);
		$duel->getWorldManager()->remove($force);
	}

	public function close() : void{
		foreach($this->duels as $duel){
			$this->closeDuel($duel, true);
		}
	}
}

<?php

declare(strict_types=1);

namespace collapse\game\duel;

use collapse\game\duel\types\DuelMode;
use collapse\Practice;
use pocketmine\entity\Location;
use pocketmine\utils\Config;
use Symfony\Component\Filesystem\Path;
use function array_map;
use function array_rand;
use function file_exists;
use function is_array;
use function is_dir;
use function mkdir;

final class DuelMapPool{

	private const string MAP_POOL_FILE_PATH = 'maps';

	private const string MAP_DIRECTORY_WORLD = 'map';
	private const string MAP_FILE_CONFIG = 'config.json';

	/** @var DuelConfig[][] */
	private array $knownMaps = [];

	private string $mapPoolFilePath;

	public function __construct(
		private readonly Practice $plugin
	){
		$this->mapPoolFilePath = Path::join($this->plugin->getDataFolder(), DuelManager::DUEL_SETTINGS_FILE_PATH, self::MAP_POOL_FILE_PATH);
		if(!is_dir($this->mapPoolFilePath)){
			mkdir($this->mapPoolFilePath, 0775, true);
		}

		/** @var \SplFileInfo $fileInfo */
		foreach(new \FilesystemIterator($this->mapPoolFilePath, \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::SKIP_DOTS) as $fileInfo){
			if(!$fileInfo->isDir()){
				continue;
			}

			$worldPath = Path::join($fileInfo->getPathname(), self::MAP_DIRECTORY_WORLD);
			$configPath = Path::join($fileInfo->getPathname(), self::MAP_FILE_CONFIG);
			if(!(is_dir($worldPath) && file_exists($configPath))){
				continue;
			}

			$definitions = (new Config($configPath, Config::JSON))->getAll();
			$name = $definitions['name'];
			$spawns = array_map(static function(array $spawn) : Location{
				return new Location($spawn['x'], $spawn['y'], $spawn['z'], null, $spawn['yaw'], $spawn['pitch']);
			}, $definitions['spawns']);
			if(is_array($definitions['mode'])){
				foreach($definitions['mode'] as $mode){
					$entry = DuelMode::from($mode);
					$this->knownMaps[$entry->value][$name] = new DuelConfig(
						$name,
						$entry,
						$worldPath,
						$spawns
					);
				}
			}else{
				$entry = DuelMode::from($definitions['mode']);
				$this->knownMaps[$entry->value][$name] = new DuelConfig(
					$name,
					$entry,
					$worldPath,
					$spawns
				);
			}
		}
	}

	public function getFilePath() : string{
		return $this->mapPoolFilePath;
	}

	public function isAnyMapsExists(DuelMode $mode) : bool{
		return isset($this->knownMaps[$mode->value]);
	}

	public function getRandom(DuelMode $mode) : DuelConfig{
		return $this->knownMaps[$mode->value][array_rand($this->knownMaps[$mode->value])];
	}
}

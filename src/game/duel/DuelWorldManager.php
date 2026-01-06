<?php

declare(strict_types=1);

namespace collapse\game\duel;

use collapse\Practice;
use collapse\world\task\WorldDeleteAsyncTask;
use pocketmine\utils\Filesystem;
use pocketmine\world\World;
use Symfony\Component\Filesystem\Path;

final class DuelWorldManager{

	private ?World $world;

	public function __construct(
		private readonly Practice $plugin,
		private readonly string $folderName
	){
		$this->plugin->getServer()->getWorldManager()->loadWorld($this->folderName);
		$this->world = $this->plugin->getServer()->getWorldManager()->getWorldByName($this->folderName);
		if($this->world === null){
			throw new \RuntimeException('Duel world ' . $this->folderName . ' not found');
		}
		$this->onWorldLoaded();
	}

	private function onWorldLoaded() : void{
		$this->world->setAutoSave(false);
		$this->world->setTime(World::TIME_DAY);
		$this->world->stopTime();
		$this->world->setDifficulty(World::DIFFICULTY_HARD);
	}

	public function getWorld() : ?World{
		return $this->world;
	}

	public function setDamageY(int $damageY) : void{
		$reflection = new \ReflectionClass($this->getWorld());
		$property = $reflection->getProperty('damageY');
		$property->setAccessible(true);
		$property->setValue($this->getWorld(), $damageY);
	}

	public function close() : void{
		if($this->world !== null && $this->world->isLoaded()){
			$this->plugin->getServer()->getWorldManager()->unloadWorld($this->world);
		}
	}

	public function remove(bool $force = false) : void{
		$filePath = Path::join($this->plugin->getServer()->getDataPath(), 'worlds', $this->folderName);
		if($force){
			Filesystem::recursiveUnlink($filePath);
		}else{
			$this->plugin->getServer()->getAsyncPool()->submitTask(new WorldDeleteAsyncTask($filePath, fn() => null));
		}
	}
}

<?php

declare(strict_types=1);

namespace collapse\game\duel\modes\skywars;

use collapse\game\duel\Duel;
use collapse\game\duel\modes\skywars\island\config\IslandConfigLoader;
use collapse\game\duel\modes\skywars\island\Island;
use collapse\game\duel\modes\skywars\island\IslandManager;
use collapse\game\duel\modes\skywars\phase\SkyWarsPhaseRunning;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\utils\EventUtils;
use Symfony\Component\Filesystem\Path;

final class SkyWars extends Duel{

	private SkyWarsTeamManager $skywarsTeamManager;

	private IslandManager $islandManager;

	protected function setUp() : void{
		EventUtils::registerListenerOnce(new SkyWarsListener());
		$this->phaseManager->setBasePhaseRunning(new SkyWarsPhaseRunning($this));
		$this->skywarsTeamManager = new SkyWarsTeamManager($this);
		$this->islandManager = new IslandManager();

		$this->initializeIslands();
		$this->islandManager->assignTeamsToStartIslands($this->getTeamManager()->getTeams());
	}

	public function onPlayerJoin(CollapsePlayer $player, \Closure $callback): void{}

	public function isBlocksActions() : bool{
		return true;
	}

	public function getSkyWarsTeamManager() : SkyWarsTeamManager{
		return $this->skywarsTeamManager;
	}

	private function initializeIslands() : void{
		$configLoader = new IslandConfigLoader();
		$configs = $configLoader->loadFromJson(Path::join(Practice::getInstance()->getDataFolder(),'duels','maps','Skywars', 'config.json'));
		foreach($configs as $config){
			$this->islandManager->addIsland(new Island($config));
		}
	}
}

<?php

declare(strict_types=1);

namespace collapse\game\duel\modes\fireball;

use collapse\game\duel\Duel;
use collapse\game\duel\modes\basic\BedManager;
use collapse\game\duel\modes\basic\BedsDuel;
use collapse\game\duel\modes\fireball\phase\FireballPhaseRunning;
use collapse\game\respawn\GameRespawnManager;
use collapse\player\CollapsePlayer;
use collapse\utils\EventUtils;
use pocketmine\block\VanillaBlocks;

final class FireballFight extends Duel implements BedsDuel{

	private const int RESPAWN_COUNTDOWN = 5;
	private const int DAMAGE_Y = 50;

	private GameRespawnManager $respawnManager;

	private BedManager $bedManager;

	protected function setUp() : void{
		EventUtils::registerListenerOnce(new FireballFightListener());
		$this->phaseManager->setBasePhaseRunning(new FireballPhaseRunning($this));
		$this->respawnManager = new GameRespawnManager($this, self::RESPAWN_COUNTDOWN);
		$this->getBlockManager()
			->addDestroyableBlock(VanillaBlocks::OAK_PLANKS())
			->addDestroyableBlock(VanillaBlocks::END_STONE())
			->addDestroyableBlock(VanillaBlocks::BED());
		$this->bedManager = new BedManager($this);
		$this->getWorldManager()->setDamageY(self::DAMAGE_Y);
	}

	public function onPlayerJoin(CollapsePlayer $player, \Closure $callback): void{}

	public function getRespawnManager() : GameRespawnManager{
		return $this->respawnManager;
	}

	public function getBedManager() : BedManager{
		return $this->bedManager;
	}

	public function isBlocksActions() : bool{
		return true;
	}
}

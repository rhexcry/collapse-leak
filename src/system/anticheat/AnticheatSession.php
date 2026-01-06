<?php

declare(strict_types=1);

namespace collapse\system\anticheat;

use collapse\player\CollapsePlayer;
use collapse\system\anticheat\check\Check;
use pocketmine\block\BlockTypeIds;
use pocketmine\math\Vector3;

class AnticheatSession{

	/** @var array<string, AnticheatSession> */
	private static array $players = [];


	private float $attackTicks = 0.0;
	private float $joinedAtTime = 0.0;
	private float $jumpTicks = 0.0;
	private float $teleportTicks = 0.0;
	private array $externalData = [];
	private bool $onAdhesion = false;
	private float $bowShotTicks = 0.0;
	private float $hurtTicks = 0.0;
	private float $projectileAttackTicks = 0.0;

	private array $violations = [];
	private array $finalViolations = [];
	private float $lastGroundY = 0.0;
	private float $lastNoGroundY = 0.0;
	private float $lastMoveTick = 0.0;
	private Vector3 $motion;

	public static function from(CollapsePlayer $player) : self{
		if(!isset(self::$players[$player->getXuid()])){
			self::$players[$player->getXuid()] = new self($player);
		}

		return self::$players[$player->getXuid()];
	}

	public function __construct(private readonly CollapsePlayer $player){
	}

	public function getPlayer() : CollapsePlayer{
		return $this->player;
	}

	public static function destroy(AnticheatSession $session) : void{
		if(isset(self::$players[$session->getPlayer()->getXuid()])){
			unset(self::$players[$session->getPlayer()->getXuid()]);
		}
	}

	public function getAttackTicks() : float{
		return (microtime(true) - $this->attackTicks) * 20;
	}

	public function setAttackTicks(float $data) : void{
		$this->attackTicks = $data;
	}

	public function getJoinedAtTheTime() : float{
		return $this->joinedAtTime;
	}

	public function setJoinedAtTheTime(float $data) : void{
		$this->joinedAtTime = $data;
	}

	public function getOnlineTime() : int{
		if($this->joinedAtTime < 1){
			return 0;
		}
		return (int) (microtime(true) - $this->joinedAtTime);
	}

	public function getTeleportTicks() : float{
		return (microtime(true) - $this->teleportTicks) * 20;
	}

	public function setTeleportTicks(float $data) : void{
		$this->teleportTicks = $data;
	}

	public function getJumpTicks() : float{
		return (microtime(true) - $this->jumpTicks) * 20;
	}

	public function setJumpTicks(float $data) : void{
		$this->jumpTicks = $data;
	}

	public function getExternalData(string $dataName, mixed $default = null){
		if(isset($this->externalData[$dataName])){
			return $this->externalData[$dataName];
		}
		return $default;
	}

	public function setExternalData(string $dataName, mixed $value) : void{
		$this->externalData[$dataName] = $value;
	}

	public function unsetExternalData(string $dataName) : void{
		if(isset($this->externalData[$dataName])){
			unset($this->externalData[$dataName]);
		}
	}

	public function isInWeb() : bool{
		$world = $this->getPlayer()->getWorld();
		$location = $this->getPlayer()->getLocation();
		$blocksAround = [
			$world->getBlock($location),
			$world->getBlock($location->add(0, 1, 0)),
			$world->getBlock($location->add(0, 2, 0)),
			$world->getBlock($location->subtract(0, 1, 0)),
			$world->getBlock($location->subtract(0, 2, 0))
		];
		foreach($blocksAround as $block){
			if($block->getTypeId() === BlockTypeIds::COBWEB){
				return true;
			}
		}

		return false;
	}

	public function isOnGround() : bool{
		if($this->getPlayer() === null){
			return false;
		}

		return $this->getPlayer()->isOnGround();
	}

	public function setOnGround(bool $data) : void{
		if($this->getPlayer() === null){
			return;
		}

		$this->getPlayer()->onGround = $data;
	}

	public function isOnAdhesion() : bool{
		return $this->onAdhesion;
	}

	public function setOnAdhesion(bool $data) : void{
		$this->onAdhesion = $data;
	}

	public function isCurrentChunkIsLoaded() : bool{
		return $this->getPlayer()->getWorld()->isInLoadedTerrain($this->getPlayer()->getLocation());
	}

	public function isGliding() : bool{
		$motion = $this->getPlayer()->getMotion();
		$isGliding = $this->getPlayer()->isGliding();
		$isFalling = $motion->y < 0;
		$horizontalSpeed = sqrt($motion->x ** 2 + $motion->z ** 2);

		if($isFalling && $horizontalSpeed > 0.5 || $isGliding){
			return true;
		}

		return false;
	}

	public function setProjectileAttackTicks(float $data) : void{
		$this->projectileAttackTicks = $data;
	}

	public function getProjectileAttackTicks() : float{
		return (microtime(true) - $this->projectileAttackTicks) * 20;
	}


	public function setBowShotTicks(float $data) : void{
		$this->bowShotTicks = $data;
	}

	public function getBowShotTicks() : float{
		return (microtime(true) - $this->bowShotTicks) * 20;
	}

	public function addViolation(Check $check, $count = 1) : void{
		if(!isset($this->violations[$check->getName() . $check->getSubType()])){
			$this->violations[$check->getName() . $check->getSubType()] = 0;
		}

		$this->violations[$check->getName() . $check->getSubType()] += $count;
	}

	public function getViolationsByCheck(Check $check) : int{
		return $this->violations[$check->getName() . $check->getSubType()] ?? 0;
	}

	public function getViolations() : array{
		return $this->violations;
	}

	public function addFinalViolation(Check $check, $count = 1) : void{
		if(!isset($this->finalViolations[$check->getName() . $check->getSubType()])){
			$this->finalViolations[$check->getName() . $check->getSubType()] = 0;
		}

		$this->finalViolations[$check->getName() . $check->getSubType()] += $count;
	}

	public function getFinalViolationsByCheck(Check $check) : int{
		return $this->finalViolations[$check->getName() . $check->getSubType()] ?? 0;
	}

	public function getFinalViolations() : array{
		return $this->finalViolations;
	}

	public function getLastGroundY() : float{
		return $this->lastGroundY;
	}

	public function setlastGroundY(float $data) : void{
		$this->lastGroundY = $data;
	}

	public function getLastNoGroundY() : float{
		return $this->lastNoGroundY;
	}

	public function setlastNoGroundY(float $data) : void{
		$this->lastNoGroundY = $data;
	}

	public function setLastMoveTick(float $data) : void{
		$this->lastMoveTick = $data;
	}

	public function getLastMoveTick() : float{
		return (microtime(true) - $this->lastMoveTick) * 20;
	}

	public function getMotion() : Vector3{
		return $this->motion ??= Vector3::zero();
	}

	public function setMotion(Vector3 $motion) : void{
		$this->motion = $motion;
	}

	public function setHurtTicks(float $data) : void{
		$this->hurtTicks = $data;
	}

	public function getHurtTicks() : float{
		return (microtime(true) - $this->hurtTicks) * 20;
	}
}
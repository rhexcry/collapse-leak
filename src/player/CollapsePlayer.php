<?php

declare(strict_types=1);

namespace collapse\player;

use collapse\entity\CollapseEnderPearl;
use collapse\entity\FishingHook;
use collapse\form\CollapseForm;
use collapse\game\Game;
use collapse\game\kb\KnockBack;
use collapse\game\teams\Team;
use collapse\player\profile\Profile;
use collapse\player\scoreboard\Scoreboard;
use collapse\player\scoreboard\ScoreboardSetEvent;
use collapse\PracticeConstants;
use collapse\punishments\Punishment;
use collapse\resourcepack\Font;
use collapse\utils\TeleportUtils;
use collapse\world\area\Area;
use pocketmine\color\Color;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\form\Form;
use pocketmine\lang\Translatable;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\CameraInstructionPacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\network\mcpe\protocol\types\camera\CameraFadeInstruction;
use pocketmine\network\mcpe\protocol\types\camera\CameraFadeInstructionColor;
use pocketmine\network\mcpe\protocol\types\camera\CameraFadeInstructionTime;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\world\Position;
use function mt_getrandmax;
use function mt_rand;
use function sqrt;

final class CollapsePlayer extends Player{

	private ?Profile $profile = null;
	private bool $waitingProfileLoad = false;

	private ?Scoreboard $scoreboard = null;

	private ?Game $game = null;

	private ?Game $spectatingGame = null;

	private ?Team $team = null;

	private ?KnockBack $knockBack = null;

	private ?Punishment $mutePunishment = null;

	private ?Form $currentForm = null;

	private ?FishingHook $fishingHook = null;

	/** @var Area[] */
	private array $collidedAreas = [];

	public function checkWorldValid() : bool{
		return $this->location->isValid();
	}

	public function setProfile(Profile $profile) : void{
		if($this->profile !== null){
			throw new \InvalidArgumentException('Player already have profile');
		}
		$this->profile = $profile;
		$this->profile->setPlayer($this);
	}

	public function getProfile() : ?Profile{
		return $this->profile;
	}

	public function setWaitingProfileLoad(bool $waitingProfileLoad = true) : void{
		$this->waitingProfileLoad = $waitingProfileLoad;
	}

	public function isWaitingProfileLoad() : bool{
		return $this->waitingProfileLoad;
	}

	public function setScoreboard(?Scoreboard $scoreboard) : void{
		$ev = new ScoreboardSetEvent($this, $scoreboard);
		$ev->call();
		if($ev->isCancelled()){
			return;
		}
		$this->scoreboard = $scoreboard;
		$this->scoreboard?->sendObjective();
		$this->scoreboard?->setUp();
		$this->scoreboard?->flushUpdates();
	}

	public function getScoreboard() : ?Scoreboard{
		return $this->scoreboard;
	}

	public function setGame(?Game $game) : void{
		$this->game = $game;
	}

	public function getGame() : ?Game{
		return $this->game;
	}

	public function isInGame() : bool{
		return $this->game !== null;
	}

	public function setSpectatingGame(?Game $game) : void{
		$this->spectatingGame = $game;
	}

	public function getSpectatingGame() : ?Game{
		return $this->spectatingGame;
	}

	public function isSpectatingGame() : bool{
		return $this->spectatingGame !== null;
	}

	public function setTeam(?Team $team) : void{
		if($team === null && $this->team !== null){
			$this->team->removePlayer($this);
		}
		$this->team = $team;
	}

	public function getTeam() : ?Team{
		return $this->team;
	}

	public function getCollidedAreas() : array{
		return $this->collidedAreas;
	}

	public function isInArea(Area $area) : bool{
		return isset($this->collidedAreas[$area->getId()]);
	}

	public function addCollidedArea(Area $area) : void{
		if($this->isInArea($area)){
			return;
		}
		$this->collidedAreas[$area->getId()] = $area;
		$area->addCollidedPlayer($this);
		$area->onEnter($this);
	}

	public function removeCollidedArea(Area $area) : void{
		if(!$this->isInArea($area)){
			return;
		}
		unset($this->collidedAreas[$area->getId()]);
		$area->removeCollidedPlayer($this);
		$area->onLeave($this);
	}

	public function removeAllCollidedAreas() : void{
		foreach($this->collidedAreas as $area){
			$area->removeCollidedPlayer($this);
		}
		$this->collidedAreas = [];
	}

	public function setKnockBack(?KnockBack $knockBack) : void{
		$this->knockBack = $knockBack;
	}

	public function getNameWithRankColor() : string{
		if($this->profile === null){
			return $this->getName();
		}
		return Font::minecraftColorToUnicodeFont($this->profile->getRank()->toColor() . $this->getName());
	}

	public function attack(EntityDamageEvent $source) : void{
		if(
			$this->knockBack !== null &&
			$source instanceof EntityDamageByEntityEvent &&
			$source->getCause() === EntityDamageEvent::CAUSE_ENTITY_ATTACK &&
			$source->getDamager() instanceof CollapsePlayer
		){
			$source->setAttackCooldown($this->knockBack->getAttackCooldown());
		}
		parent::attack($source);
	}

	public function knockBack(float $x, float $z, float $force = self::DEFAULT_KNOCKBACK_FORCE, ?float $verticalLimit = self::DEFAULT_KNOCKBACK_VERTICAL_LIMIT) : void{
		if(!(
			$this->knockBack !== null &&
			((
				$this->lastDamageCause instanceof EntityDamageByEntityEvent &&
				$this->lastDamageCause->getCause() === EntityDamageEvent::CAUSE_ENTITY_ATTACK &&
				$this->lastDamageCause->getDamager() instanceof CollapsePlayer
			) || (
				$this->lastDamageCause instanceof EntityDamageByChildEntityEvent &&
				$this->lastDamageCause->getCause() === EntityDamageEvent::CAUSE_PROJECTILE &&
				$this->lastDamageCause->getChild() instanceof CollapseEnderPearl
			))
		)){
			parent::knockBack($x, $z, $force, $verticalLimit);
			return;
		}

		$f = sqrt($x * $x + $z * $z);
		if($f <= 0){
			return;
		}

		$force = $this->knockBack->getHorizontal();
		if(mt_rand() / mt_getrandmax() > $this->knockbackResistanceAttr->getValue()){
			$f = 1 / $f;

			$vertical = $this->knockBack->getVertical();
			if($this->lastDamageCause instanceof EntityDamageByChildEntityEvent){
				$force = 0.4;
				$vertical = 0.5;
			}

			$motionX = $this->motion->x / 2;
			$motionY = $this->motion->y / 2;
			$motionZ = $this->motion->z / 2;
			$motionX += $x * $f * $force;
			$motionY += $vertical;
			$motionZ += $z * $f * $force;

			if($this->lastDamageCause instanceof EntityDamageByEntityEvent){
				$dist = $this->location->y - $this->lastDamageCause->getDamager()->getLocation()->getY();
				if($dist >= 4.0){
					$vertical -= $dist / 38.795;
				}
			}

			if($motionY > $vertical){
				$motionY = $vertical;
			}

			$this->setMotion(new Vector3($motionX, $motionY, $motionZ));
		}
	}

	public function setBasicProperties(GameMode $gameMode) : void{
		$this->setAllowFlight(false);
		$this->setFlying(false);
		$this->setGamemode($gameMode);
		$this->setHealth($this->getMaxHealth());
		$this->getEffects()->clear();
		$this->getArmorInventory()->clearAll();
		$this->getCraftingGrid()->clearAll();
		$this->getCursorInventory()->clearAll();
		$this->getInventory()->clearAll();
		$this->getOffHandInventory()->clearAll();
		$this->getHungerManager()->setFood($this->getHungerManager()->getMaxFood());
		$this->extinguish();
	}

	public function sendTranslatedMessage(Translatable $entry, bool $prefix = true) : void{
		if(!$this->isConnected()){
			return;
		}
		$message = $this->profile?->getTranslator()?->translate($entry);
		if($message === null){
			return;
		}
		$this->sendMessage($message, $prefix);
	}

	public function sendTranslatedPopup(Translatable $entry) : void{
		if(!$this->isConnected()){
			return;
		}
		$message = $this->profile?->getTranslator()?->translate($entry);
		if($message === null){
			return;
		}
		$this->sendPopup($message);
	}

	public function sendTranslatedTitle(Translatable $title, ?Translatable $subtitle = null, int $fadeIn = -1, int $stay = -1, int $fadeOut = -1) : void{
		if(!$this->isConnected()){
			return;
		}
		$translator = $this->profile?->getTranslator();
		if($translator === null){
			return;
		}
		parent::sendTitle($translator->translate($title), $subtitle === null ? '' : $translator->translate($subtitle), $fadeIn, $stay, $fadeOut);
	}

	public function sendMessage(Translatable|string $message, bool $prefix = true) : void{
		if($prefix){
			if($message instanceof Translatable){
				$message = new Translatable(PracticeConstants::CHAT_MESSAGE_PREFIX . '{%0}', [new Translatable($message->getText(), $message->getParameters())]);
			}else{
				$message = PracticeConstants::CHAT_MESSAGE_PREFIX . $message;
			}
		}
		parent::sendMessage($message);
	}

	public function sendPosition(Vector3 $pos, ?float $yaw = null, ?float $pitch = null, int $mode = MovePlayerPacket::MODE_NORMAL) : void{
		parent::sendPosition($pos, $yaw, $pitch, $mode);
	}

	public function getMutePunishment() : ?Punishment{
		if($this->mutePunishment !== null && $this->mutePunishment->isActive()){
			return $this->mutePunishment;
		}

		$this->mutePunishment = null;

		return null;
	}

	public function setMutePunishment(?Punishment $punishment) : void{
		$this->mutePunishment = $punishment;
	}

	public function sendForm(Form $form) : void{
		if($form instanceof CollapseForm){
			$form->processForPlayer($this);
		}
		parent::sendForm($form);
		$this->currentForm = $form;
	}

	public function getCurrentForm() : ?Form{
		return $this->currentForm;
	}

	public function onFormSubmit(int $formId, mixed $responseData) : bool{
		$response = parent::onFormSubmit($formId, $responseData);
		$this->currentForm = null;
		return $response;
	}

	public function setFishingHook(?FishingHook $fishingHook) : void{
		$this->fishingHook = $fishingHook;
	}

	public function getFishingHook() : ?FishingHook{
		return $this->fishingHook;
	}

	public function teleport(Vector3 $pos, ?float $yaw = null, ?float $pitch = null, bool $safe = true) : bool{
		if($safe && ($pos instanceof Position)){
			return TeleportUtils::safeTeleport($this, $pos);
		}
		return parent::teleport($pos, $yaw, $pitch);
	}

	public function setFade(int|float $fadeInTime = 0, int|float $stayTime = 0.5, int|float $fadeOutTime = 0.5) : void{
		if(!$this->isConnected()){
			return;
		}

		$color = new Color(0, 0, 0);

		$this->getNetworkSession()->sendDataPacket(CameraInstructionPacket::create(
			null,
			null,
			new CameraFadeInstruction(
				new CameraFadeInstructionTime($fadeInTime, $stayTime, $fadeOutTime),
				new CameraFadeInstructionColor($color->getR(), $color->getG(), $color->getB())
			),
			null,
			null,
			null,
			null,
			null,
			null
		));
	}
}

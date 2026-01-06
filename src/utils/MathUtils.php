<?php

declare(strict_types=1);

namespace collapse\utils;

use collapse\player\CollapsePlayer;
use collapse\system\anticheat\AnticheatSession;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\math\Vector3;

final class MathUtils{

	private const float SPRINTING_SPEED = 1.3;
	private const float SNEAKING_BASE_SPEED = 0.3;
	private const float SNEAKING_ENCHANTMENT_MULTIPLIER = 0.15;
	private const float USING_ITEM_SPEED = 0.2;
	private const float DEFAULT_SPEED = 1.0;

	private const float SPEED_EFFECT_MULTIPLIER = 0.2;
	private const float SLOWNESS_EFFECT_MULTIPLIER = 0.15;

	private const float AIR_ACCELERATION = 0.02;
	private const float GROUND_ACCELERATION_BASE = 0.1;
	private const float FRICTION_DIVISOR = 0.6;
	private const int FRICTION_POWER = 3;
	private const float MOMENTUM_FRICTION = 0.91;

	public static function getMovement(CollapsePlayer $player) : float{
		if($player->isUsingItem()){
			return self::USING_ITEM_SPEED;
		}

		if($player->isSprinting()){
			return self::SPRINTING_SPEED;
		}

		if($player->isSneaking()){
			$armorLeggings = $player->getArmorInventory()->getLeggings();
			$swiftSneakLevel = $armorLeggings->getEnchantmentLevel(VanillaEnchantments::SWIFT_SNEAK());

			return self::SNEAKING_BASE_SPEED + (self::SNEAKING_ENCHANTMENT_MULTIPLIER * $swiftSneakLevel);
		}

		return self::DEFAULT_SPEED;
	}

	public static function getEffectsMultiplier(CollapsePlayer $player) : float{
		$effects = $player->getEffects();

		$speedLevel = self::getEffectLevel($effects->get(VanillaEffects::SPEED()));
		$slownessLevel = self::getEffectLevel($effects->get(VanillaEffects::SLOWNESS()));

		$speedMultiplier = 1.0 + (self::SPEED_EFFECT_MULTIPLIER * $speedLevel);
		$slownessMultiplier = 1.0 - (self::SLOWNESS_EFFECT_MULTIPLIER * $slownessLevel);

		return $speedMultiplier * $slownessMultiplier;
	}

	private static function getEffectLevel(?EffectInstance $effect) : int{
		return $effect !== null ? $effect->getEffectLevel() : 0;
	}

	public static function getMomentum(float $lastDistance, float $friction) : float{
		return $lastDistance * $friction * self::MOMENTUM_FRICTION;
	}

	public static function getAcceleration(float $movement, float $effectMultiplier, float $friction, bool $onGround) : float{
		if(!$onGround){
			return self::AIR_ACCELERATION * $movement;
		}

		$frictionFactor = (self::FRICTION_DIVISOR / $friction) ** self::FRICTION_POWER;
		return self::GROUND_ACCELERATION_BASE * $movement * $effectMultiplier * $frictionFactor;
	}

	public static function getVectorOnEyeHeight(AnticheatSession $session) : Vector3{
		$player = $session->getPlayer();
		return $player->getLocation()->add(0, $player->getEyeHeight(), 0);
	}

	public static function getDeltaDirectionVector(AnticheatSession $session, float $distance) : Vector3{
		$player = $session->getPlayer();
		$directionVector = $player->getDirectionVector();

		return $directionVector !== null ? $directionVector->multiply($distance) : new Vector3(0, 0, 0);
	}

	public static function distance(Vector3 $from, Vector3 $to) : float{
		$dx = $from->getX() - $to->getX();
		$dy = $from->getY() - $to->getY();
		$dz = $from->getZ() - $to->getZ();

		return sqrt($dx * $dx + $dy * $dy + $dz * $dz);
	}

	public static function distanceSquared(Vector3 $from, Vector3 $to) : float{
		$dx = $from->getX() - $to->getX();
		$dy = $from->getY() - $to->getY();
		$dz = $from->getZ() - $to->getZ();

		return $dx * $dx + $dy * $dy + $dz * $dz;
	}

	public static function pingFormula(float $ping) : int{
		return (int) ceil($ping / 50.0);
	}

	public static function XZDistanceSquared(Vector3 $v1, Vector3 $v2) : float{
		$dx = $v1->x - $v2->x;
		$dz = $v1->z - $v2->z;

		return $dx * $dx + $dz * $dz;
	}

	public static function XZDistance(Vector3 $v1, Vector3 $v2) : float{
		return sqrt(self::XZDistanceSquared($v1, $v2));
	}

	public static function horizontalAngleBetween(Vector3 $from, Vector3 $to) : float{
		$dx = $to->x - $from->x;
		$dz = $to->z - $from->z;

		return atan2($dz, $dx) * (180 / M_PI);
	}

	public static function clamp(float $value, float $min, float $max) : float{
		return max($min, min($max, $value));
	}

	public static function lerp(float $start, float $end, float $progress) : float{
		return $start + $progress * ($end - $start);
	}

	public static function isWithinTolerance(float $value1, float $value2, float $tolerance) : bool{
		return abs($value1 - $value2) <= $tolerance;
	}
}
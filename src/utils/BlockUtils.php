<?php

declare(strict_types=1);

namespace collapse\utils;

use collapse\player\CollapsePlayer;
use pocketmine\block\Block;
use pocketmine\block\BlockTypeIds;
use pocketmine\entity\Location;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\world\Position;
use function abs;
use function array_flip;
use function fmod;
use function md5;
use function serialize;
use function sqrt;

final class BlockUtils{
	private const float FRACTION_THRESHOLD_LOW = 0.3;
	private const float FRACTION_THRESHOLD_HIGH = 0.7;
	private const int GROUND_CHECK_RADIUS = 2;

	private static ?array $stairsCache = null;
	private static ?array $iceCache = null;
	private static ?array $liquidCache = null;
	private static ?array $adhesionCache = null;
	private static ?array $plantsCache = null;
	private static ?array $doorsCache = null;
	private static ?array $carpetsCache = null;
	private static ?array $platesCache = null;
	private static ?array $snowCache = null;
	private static array $idMaps = [];

	public static function getBlockAbove(CollapsePlayer $player) : ?Block{
		$position = $player->getPosition()->add(0, 1.0, 0);
		return $player->getWorld()->getBlock($position->getSide(Facing::UP));
	}

	public static function isGroundSolid(CollapsePlayer $player) : bool{
		$world = $player->getWorld();
		$pos = $player->getPosition();
		$checkY = (int) ($pos->y - 1);

		for($x = -self::GROUND_CHECK_RADIUS; $x <= self::GROUND_CHECK_RADIUS; $x++){
			for($z = -self::GROUND_CHECK_RADIUS; $z <= self::GROUND_CHECK_RADIUS; $z++){
				$block = $world->getBlockAt(
					(int) ($pos->x + $x),
					$checkY,
					(int) ($pos->z + $z)
				);

				if(!$block->isSolid()){
					return false;
				}
			}
		}

		return true;
	}

	public static function getSurroundingBlocks(CollapsePlayer $player) : array{
		$world = $player->getWorld();
		$pos = $player->getLocation();

		$blocks = [];
		$offsets = [
			[0, 0, 0],
			[-1, 0, 0],
			[-1, 0, -1],
			[0, 0, -1],
			[1, 0, 0],
			[1, 0, 1],
			[0, 0, 1],
			[1, 0, -1],
			[-1, 0, 1]
		];

		foreach($offsets as $offset){
			$blockPos = new Vector3(
				$pos->x + $offset[0],
				$pos->y,
				$pos->z + $offset[2]
			);
			$blocks[] = $world->getBlock($blockPos)->getTypeId();
		}

		return $blocks;
	}

	public static function isOnGround(Location $location, int $down) : bool{
		$world = $location->getWorld();
		$blockY = (int) ($location->getY() - $down);

		$posX = $location->getX();
		$posZ = $location->getZ();

		$fracX = fmod($posX, 1.0);
		$fracZ = fmod($posZ, 1.0);

		$fracX = $fracX < 0 ? 1.0 + $fracX : $fracX;
		$fracZ = $fracZ < 0 ? 1.0 + $fracZ : $fracZ;

		$checkBlock = function(int $x, int $z) use ($world, $blockY) : bool{
			return $world->getBlockAt($x, $blockY, $z)->getTypeId() !== BlockTypeIds::AIR;
		};

		$baseX = (int) $posX;
		$baseZ = (int) $posZ;

		if($checkBlock($baseX, $baseZ)){
			return true;
		}

		if($fracX < self::FRACTION_THRESHOLD_LOW){
			if($checkBlock($baseX - 1, $baseZ)) return true;

			if($fracZ < self::FRACTION_THRESHOLD_LOW){
				return $checkBlock($baseX - 1, $baseZ - 1) ||
					$checkBlock($baseX, $baseZ - 1) ||
					$checkBlock($baseX + 1, $baseZ - 1);
			}
			if($fracZ > self::FRACTION_THRESHOLD_HIGH){
				return $checkBlock($baseX - 1, $baseZ + 1) ||
					$checkBlock($baseX, $baseZ + 1) ||
					$checkBlock($baseX + 1, $baseZ + 1);
			}
		}elseif($fracX > self::FRACTION_THRESHOLD_HIGH){
			if($checkBlock($baseX + 1, $baseZ)) return true;

			if($fracZ < self::FRACTION_THRESHOLD_LOW){
				return $checkBlock($baseX - 1, $baseZ - 1) ||
					$checkBlock($baseX, $baseZ - 1) ||
					$checkBlock($baseX + 1, $baseZ - 1);
			}
			if($fracZ > self::FRACTION_THRESHOLD_HIGH){
				return $checkBlock($baseX - 1, $baseZ + 1) ||
					$checkBlock($baseX, $baseZ + 1) ||
					$checkBlock($baseX + 1, $baseZ + 1);
			}
		}elseif($fracZ < self::FRACTION_THRESHOLD_LOW){
			return $checkBlock($baseX, $baseZ - 1);
		}elseif($fracZ > self::FRACTION_THRESHOLD_HIGH){
			return $checkBlock($baseX, $baseZ + 1);
		}

		return false;
	}

	public static function isUnderBlock(Location $location, array $ids, int $down) : bool{
		$hash = md5(serialize($ids));
		if(!isset(self::$idMaps[$hash])){
			self::$idMaps[$hash] = array_flip($ids);
		}
		$idMap = self::$idMaps[$hash];

		$world = $location->getWorld();
		$blockY = (int) ($location->getY() - $down);

		$posX = $location->getX();
		$posZ = $location->getZ();

		$fracX = fmod($posX, 1.0);
		$fracZ = fmod($posZ, 1.0);

		$fracX = $fracX < 0 ? 1.0 + $fracX : $fracX;
		$fracZ = $fracZ < 0 ? 1.0 + $fracZ : $fracZ;

		$check = function(int $x, int $z) use ($world, $blockY, $idMap) : bool{
			return isset($idMap[$world->getBlockAt($x, $blockY, $z)->getTypeId()]);
		};

		$baseX = (int) $posX;
		$baseZ = (int) $posZ;

		if($check($baseX, $baseZ)){
			return true;
		}

		if($fracX < self::FRACTION_THRESHOLD_LOW){
			if($check($baseX - 1, $baseZ)){
				return true;
			}

			if($fracZ < self::FRACTION_THRESHOLD_LOW){
				return $check($baseX - 1, $baseZ - 1) ||
					$check($baseX, $baseZ - 1) ||
					$check($baseX + 1, $baseZ - 1);
			}elseif($fracZ > self::FRACTION_THRESHOLD_HIGH){
				return $check($baseX - 1, $baseZ + 1) ||
					$check($baseX, $baseZ + 1) ||
					$check($baseX + 1, $baseZ + 1);
			}
		}elseif($fracX > self::FRACTION_THRESHOLD_HIGH){
			if($check($baseX + 1, $baseZ)){
				return true;
			}

			if($fracZ < self::FRACTION_THRESHOLD_LOW){
				return $check($baseX - 1, $baseZ - 1) ||
					$check($baseX, $baseZ - 1) ||
					$check($baseX + 1, $baseZ - 1);
			}elseif($fracZ > self::FRACTION_THRESHOLD_HIGH){
				return $check($baseX - 1, $baseZ + 1) ||
					$check($baseX, $baseZ + 1) ||
					$check($baseX + 1, $baseZ + 1);
			}
		}elseif($fracZ < self::FRACTION_THRESHOLD_LOW){
			return $check($baseX, $baseZ - 1);
		}elseif($fracZ > self::FRACTION_THRESHOLD_HIGH){
			return $check($baseX, $baseZ + 1);
		}

		return false;
	}

	private static function getStairsIds() : array{
		if(self::$stairsCache === null){
			self::$stairsCache = [
				BlockTypeIds::STONE_STAIRS,
				BlockTypeIds::OAK_STAIRS,
				BlockTypeIds::BIRCH_STAIRS,
				BlockTypeIds::BRICK_STAIRS,
				BlockTypeIds::STONE_BRICK_STAIRS,
				BlockTypeIds::ACACIA_STAIRS,
				BlockTypeIds::JUNGLE_STAIRS,
				BlockTypeIds::PURPUR_STAIRS,
				BlockTypeIds::QUARTZ_STAIRS,
				BlockTypeIds::SPRUCE_STAIRS,
				BlockTypeIds::DIORITE_STAIRS,
				BlockTypeIds::GRANITE_STAIRS,
				BlockTypeIds::ANDESITE_STAIRS,
				BlockTypeIds::DARK_OAK_STAIRS,
				BlockTypeIds::END_STONE_BRICKS,
				BlockTypeIds::SANDSTONE_STAIRS,
				BlockTypeIds::PRISMARINE_STAIRS,
				BlockTypeIds::COBBLESTONE_STAIRS,
				BlockTypeIds::NETHER_BRICK_STAIRS,
				BlockTypeIds::RED_SANDSTONE_STAIRS,
				BlockTypeIds::SMOOTH_QUARTZ_STAIRS,
				BlockTypeIds::DARK_PRISMARINE_STAIRS,
				BlockTypeIds::POLISHED_DIORITE_STAIRS,
				BlockTypeIds::POLISHED_GRANITE_STAIRS,
				BlockTypeIds::RED_NETHER_BRICK_STAIRS,
				BlockTypeIds::SMOOTH_SANDSTONE_STAIRS,
				BlockTypeIds::MOSSY_COBBLESTONE_STAIRS,
				BlockTypeIds::MOSSY_STONE_BRICK_STAIRS,
				BlockTypeIds::POLISHED_ANDESITE_STAIRS,
				BlockTypeIds::PRISMARINE_BRICKS_STAIRS,
				BlockTypeIds::SMOOTH_RED_SANDSTONE_STAIRS
			];
		}
		return self::$stairsCache;
	}

	public static function isOnStairs(Location $location, int $down) : bool{
		return self::isUnderBlock($location, self::getStairsIds(), $down);
	}

	private static function getIceIds() : array{
		if(self::$iceCache === null){
			self::$iceCache = [
				BlockTypeIds::ICE,
				BlockTypeIds::BLUE_ICE,
				BlockTypeIds::PACKED_ICE,
				BlockTypeIds::FROSTED_ICE
			];
		}
		return self::$iceCache;
	}

	public static function isOnIce(Location $location, int $down) : bool{
		return self::isUnderBlock($location, self::getIceIds(), $down);
	}

	private static function getLiquidIds() : array{
		if(self::$liquidCache === null){
			self::$liquidCache = [
				BlockTypeIds::WATER,
				BlockTypeIds::LAVA
			];
		}
		return self::$liquidCache;
	}

	public static function isOnLiquid(Location $location, int $down) : bool{
		return self::isUnderBlock($location, self::getLiquidIds(), $down);
	}

	private static function getAdhesionIds() : array{
		if(self::$adhesionCache === null){
			self::$adhesionCache = [
				BlockTypeIds::LADDER,
				BlockTypeIds::VINES
			];
		}
		return self::$adhesionCache;
	}

	public static function isOnAdhesion(Location $location, int $down) : bool{
		return self::isUnderBlock($location, self::getAdhesionIds(), $down);
	}

	private static function getPlantsIds() : array{
		if(self::$plantsCache === null){
			self::$plantsCache = [
				BlockTypeIds::GRASS_PATH,
				BlockTypeIds::CARROTS,
				BlockTypeIds::SUGARCANE,
				BlockTypeIds::PUMPKIN_STEM,
				BlockTypeIds::POTATOES,
				BlockTypeIds::DEAD_BUSH,
				BlockTypeIds::SWEET_BERRY_BUSH,
				BlockTypeIds::OAK_SAPLING,
				BlockTypeIds::WHEAT,
				BlockTypeIds::TALL_GRASS,
				BlockTypeIds::TORCHFLOWER,
				BlockTypeIds::CHORUS_FLOWER,
				BlockTypeIds::CORNFLOWER,
				BlockTypeIds::TORCHFLOWER_CROP,
				BlockTypeIds::FLOWERING_AZALEA_LEAVES,
				BlockTypeIds::FLOWER_POT
			];
		}
		return self::$plantsCache;
	}

	public static function isOnPlant(Location $location, int $down) : bool{
		return self::isUnderBlock($location, self::getPlantsIds(), $down);
	}

	private static function getDoorsIds() : array{
		if(self::$doorsCache === null){
			self::$doorsCache = [
				BlockTypeIds::OAK_DOOR,
				BlockTypeIds::IRON_DOOR,
				BlockTypeIds::DARK_OAK_DOOR,
				BlockTypeIds::BIRCH_DOOR,
				BlockTypeIds::ACACIA_DOOR,
				BlockTypeIds::JUNGLE_DOOR,
				BlockTypeIds::SPRUCE_DOOR,
				BlockTypeIds::DARK_OAK_TRAPDOOR,
				BlockTypeIds::OAK_TRAPDOOR,
				BlockTypeIds::IRON_TRAPDOOR,
				BlockTypeIds::BIRCH_TRAPDOOR,
				BlockTypeIds::ACACIA_TRAPDOOR,
				BlockTypeIds::JUNGLE_TRAPDOOR,
				BlockTypeIds::SPRUCE_TRAPDOOR
			];
		}
		return self::$doorsCache;
	}

	public static function isOnDoor(Location $location, int $down) : bool{
		return self::isUnderBlock($location, self::getDoorsIds(), $down);
	}

	private static function getCarpetsIds() : array{
		if(self::$carpetsCache === null){
			self::$carpetsCache = [
				BlockTypeIds::CARPET
			];
		}
		return self::$carpetsCache;
	}

	public static function isOnCarpet(Location $location, int $down) : bool{
		return self::isUnderBlock($location, self::getCarpetsIds(), $down);
	}

	private static function getPlatesIds() : array{
		if(self::$platesCache === null){
			self::$platesCache = [
				BlockTypeIds::CARPET,
				BlockTypeIds::BIRCH_PRESSURE_PLATE,
				BlockTypeIds::STONE_PRESSURE_PLATE,
				BlockTypeIds::ACACIA_PRESSURE_PLATE,
				BlockTypeIds::JUNGLE_PRESSURE_PLATE,
				BlockTypeIds::SPRUCE_PRESSURE_PLATE,
				BlockTypeIds::OAK_PRESSURE_PLATE,
				BlockTypeIds::DARK_OAK_PRESSURE_PLATE,
				BlockTypeIds::WEIGHTED_PRESSURE_PLATE_HEAVY,
				BlockTypeIds::WEIGHTED_PRESSURE_PLATE_LIGHT
			];
		}
		return self::$platesCache;
	}

	public static function isOnPlate(Location $location, int $down) : bool{
		return self::isUnderBlock($location, self::getPlatesIds(), $down);
	}

	private static function getSnowIds() : array{
		if(self::$snowCache === null){
			self::$snowCache = [
				BlockTypeIds::SNOW,
				BlockTypeIds::SNOW_LAYER
			];
		}
		return self::$snowCache;
	}

	public static function isOnSnow(Location $location, int $down) : bool{
		return self::isUnderBlock($location, self::getSnowIds(), $down);
	}

	public static function onSlimeBlock(Location $location, int $down) : bool{
		return self::isUnderBlock($location, [BlockTypeIds::SLIME], $down);
	}

	public static function getUnderBlock(Location $location, int $deep = 1) : Block{
		return $location->getWorld()->getBlockAt(
			abs((int) $location->getX()),
			abs((int) $location->getY()) - $deep,
			abs((int) $location->getZ())
		);
	}

	public static function distance(Position $a, Position $b) : float{
		$dx = $a->getX() - $b->getX();
		$dy = $a->getY() - $b->getY();
		$dz = $a->getZ() - $b->getZ();

		return sqrt($dx * $dx + $dy * $dy + $dz * $dz);
	}
}
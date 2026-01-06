<?php

declare(strict_types=1);

namespace collapse\system\anticheat\check\fly;

use collapse\player\CollapsePlayer;
use collapse\system\anticheat\AnticheatConstants;
use collapse\system\anticheat\AnticheatSession;
use collapse\system\anticheat\check\Check;
use collapse\utils\BlockUtils;
use pocketmine\block\BlockTypeIds;
use pocketmine\event\Event;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\world\Position;

final class FlyB extends Check{
	private const array FENCE_BLOCKS = [
		BlockTypeIds::OAK_FENCE,
		BlockTypeIds::COBBLESTONE_WALL,
		BlockTypeIds::ACACIA_FENCE,
		BlockTypeIds::BIRCH_FENCE,
		BlockTypeIds::DARK_OAK_FENCE,
		BlockTypeIds::JUNGLE_FENCE,
		BlockTypeIds::NETHER_BRICK_FENCE,
		BlockTypeIds::SPRUCE_FENCE,
		BlockTypeIds::WARPED_FENCE,
		BlockTypeIds::MANGROVE_FENCE,
		BlockTypeIds::CRIMSON_FENCE,
		BlockTypeIds::CHERRY_FENCE,
		BlockTypeIds::ACACIA_FENCE_GATE,
		BlockTypeIds::OAK_FENCE_GATE,
		BlockTypeIds::BIRCH_FENCE_GATE,
		BlockTypeIds::DARK_OAK_FENCE_GATE,
		BlockTypeIds::JUNGLE_FENCE_GATE,
		BlockTypeIds::SPRUCE_FENCE_GATE,
		BlockTypeIds::WARPED_FENCE_GATE,
		BlockTypeIds::MANGROVE_FENCE_GATE,
		BlockTypeIds::CRIMSON_FENCE_GATE,
		BlockTypeIds::CHERRY_FENCE_GATE,
		BlockTypeIds::GLASS_PANE,
		BlockTypeIds::HARDENED_GLASS_PANE,
		BlockTypeIds::STAINED_GLASS_PANE,
		BlockTypeIds::STAINED_HARDENED_GLASS_PANE
	];

	private static ?array $fenceBlocksMap = null;

	public function getName() : string{
		return 'Fly';
	}

	public function getSubType() : string{
		return 'B';
	}

	public function checkEvent(Event $event, AnticheatSession $session) : void{
		if(!$event instanceof PlayerMoveEvent){
			return;
		}

		$player = $session->getPlayer();
		$oldPos = $event->getFrom();
		$newPos = $event->getTo();

		if($this->shouldSkipCheck($session, $player)){
			return;
		}

		if($oldPos->getY() <= $newPos->getY() &&
			$player->getInAirTicks() > AnticheatConstants::MAX_AIR_TICKS){

			$this->checkFlying($session, $player, $newPos);
		}
	}

	private function shouldSkipCheck(AnticheatSession $session, CollapsePlayer $player) : bool{
		return $session->getAttackTicks() < 40 ||
			$session->isInWeb() ||
			$session->isOnGround() ||
			$session->isOnAdhesion() ||
			$player->getAllowFlight() ||
			$player->hasNoClientPredictions() ||
			!$player->isSurvival() ||
			!$session->isCurrentChunkIsLoaded() ||
			BlockUtils::isGroundSolid($player) ||
			$session->isGliding() ||
			$player->isCreative() ||
			$player->isSpectator();
	}

	private function checkFlying(AnticheatSession $session, CollapsePlayer $player, Position $newPos) : void{
		$maxY = $player->getWorld()->getHighestBlockAt(
			(int) $newPos->getX(),
			(int) $newPos->getZ()
		);

		if($newPos->getY() - 1 > $maxY){
			$surroundingBlocks = BlockUtils::getSurroundingBlocks($player);

			if(self::$fenceBlocksMap === null){
				self::$fenceBlocksMap = array_flip(self::FENCE_BLOCKS);
			}

			$hasFenceBlocks = false;
			foreach($surroundingBlocks as $blockId){
				if(isset(self::$fenceBlocksMap[$blockId])){
					$hasFenceBlocks = true;
					break;
				}
			}

			if(!$hasFenceBlocks){
				$this->failed($session);
				$this->debug($session, 'newY=' . $newPos->getY() . ', airTicks=' . $player->getInAirTicks() . ', maxY=' . $maxY . ', surroundingBlocks=' . count($surroundingBlocks));
			}
		}
	}

	public function getMaxViolations() : int{
		return 1;
	}

	public function getMaxFinalViolations() : int{
		return 1;
	}
}
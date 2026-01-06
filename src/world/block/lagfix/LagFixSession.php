<?php

declare(strict_types=1);

namespace collapse\world\block\lagfix;

use collapse\player\CollapsePlayer;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\types\inventory\PredictedResult;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemTransactionData;
use pocketmine\network\PacketHandlingException;
use WeakMap;
use WeakReference;
use function array_push;
use function in_array;
use function microtime;

class LagFixSession{

	/** @var WeakMap<CollapsePlayer, LagFixSession> */
	private static WeakMap $sessions;

	/** @var WeakReference<CollapsePlayer> */
	private WeakReference $player;

	protected float $lastRightClickTime = 0.0;
	protected ?UseItemTransactionData $lastRightClickData = null;

	public function __construct(CollapsePlayer $player){
		$this->player = WeakReference::create($player);
	}

	public static function get(CollapsePlayer $player) : self{
		if(!isset(self::$sessions)){
			self::$sessions = new WeakMap();
		}

		return self::$sessions[$player] ??= new self($player);
	}

	public function handleClickBlockTransaction(UseItemTransactionData $data) : void{
		try{
			$this->player->get()->selectHotbarSlot($data->getHotbarSlot());
			$clickPos = $data->getClickPosition();
			$hasPrediction = $this->isHaveClientInteractPrediction();
			$spamBug = ($this->lastRightClickData !== null &&
				microtime(true) - $this->lastRightClickTime < 0.1 && //100ms
				$this->lastRightClickData->getPlayerPosition()->distanceSquared($data->getPlayerPosition()) < 0.00001 &&
				$this->lastRightClickData->getBlockPosition()->equals($data->getBlockPosition()) &&
				$this->lastRightClickData->getClickPosition()->distanceSquared($clickPos) < 0.00001 &&
				(!$hasPrediction || $data->getClientInteractPrediction() === PredictedResult::FAILURE)
			);

			$this->lastRightClickData = $data;
			$this->lastRightClickTime = microtime(true);

			if($spamBug){
				return;
			}

			self::validateFacing($data->getFace());

			$blockPos = $data->getBlockPosition();
			$vBlockPos = new Vector3($blockPos->getX(), $blockPos->getY(), $blockPos->getZ());
			if(!$this->player->get()->interactBlock($vBlockPos, $data->getFace(), $clickPos) && (!$hasPrediction || !$this->isFailedPrediction($data))){
				$this->onFailedBlockAction($vBlockPos, $data->getFace());
			}
		}catch(\Exception $exception){}
	}

	private static function validateFacing(int $facing) : void{
		if(!in_array($facing, Facing::ALL, true)){
			throw new PacketHandlingException("Invalid facing value $facing");
		}
	}

	public function isHaveClientInteractPrediction() : bool{
		return $this->player->get()->getNetworkSession()->getProtocolId() >= ProtocolInfo::PROTOCOL_1_21_20;
	}

	private function isFailedPrediction(UseItemTransactionData $data) : bool{
		return $data->getClientInteractPrediction() === PredictedResult::FAILURE;
	}

	private function onFailedBlockAction(Vector3 $blockPos, ?int $face) : void{
		if($blockPos->distanceSquared($this->player->get()->getLocation()) < 10000){
			$blocks = $blockPos->sidesArray();
			if($face !== null){
				$sidePos = $blockPos->getSide($face);

				array_push($blocks, ...$sidePos->sidesArray());
			}else{
				$blocks[] = $blockPos;
			}
			foreach($this->player->get()->getWorld()->createBlockUpdatePackets(TypeConverter::getInstance($this->player->get()->getNetworkSession()->getProtocolId()), $blocks) as $packet){
				$this->player->get()->getNetworkSession()->sendDataPacket($packet);
			}
		}
	}
}
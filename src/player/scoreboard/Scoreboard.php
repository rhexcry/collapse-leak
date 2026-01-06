<?php

declare(strict_types=1);

namespace collapse\player\scoreboard;

use collapse\player\CollapsePlayer;
use pocketmine\lang\Translatable;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\utils\TextFormat;
use function count;

abstract class Scoreboard{

	private array $lines = [];
	private array $dirtyLines = [];
	private bool $recentlySet = false;

	public function __construct(
		protected readonly CollapsePlayer $player
	){}

	final public function sendObjective() : void{
		$this->player->getNetworkSession()->sendDataPacket(SetDisplayObjectivePacket::create(
			SetDisplayObjectivePacket::DISPLAY_SLOT_SIDEBAR,
			'dummy',
			$this->title(),
			'dummy',
			SetDisplayObjectivePacket::SORT_ORDER_ASCENDING
		));
		$this->recentlySet = true;
	}

	final public function remove() : void{
		$this->player->getNetworkSession()->sendDataPacket(RemoveObjectivePacket::create('dummy'));
	}

	abstract public function setUp() : void;

	public function getLine(int $lineNumber) : ?string{
		return $this->lines[$lineNumber] ?? null;
	}

	public function setLine(int $lineNumber, Translatable|string $message, bool $immediate = false) : void{
		if($lineNumber < 1 || $lineNumber > 15){
			throw new \InvalidArgumentException('Line ' . $lineNumber . ' out of bounds');
		}
		if($message instanceof Translatable){
			$message = $this->player->getProfile()->getTranslator()->translate($message);
		}
		$this->lines[$lineNumber] = $message;
		$this->dirtyLines[] = $lineNumber;
		if($immediate){
			$this->flushUpdates();
		}
	}

	/**
	 * @param (string[]|Translatable[]|null) $lines
	 */
	public function setLines(array $lines, bool $immediate = false) : void{
		$empty = TextFormat::BLACK;
		foreach($lines as $lineNumber => $message){
			$this->setLine($lineNumber, $message === null ? $empty : ($message instanceof Translatable ? $this->player->getProfile()->getTranslator()->translate($message) : $message));
			if($message === null){
				$empty .= $empty;
			}
		}
		if($immediate){
			$this->flushUpdates();
		}
	}

	final public function flushUpdates() : void{
		$entries = [];
		foreach($this->dirtyLines as $lineNumber){
			$entry = new ScorePacketEntry();
			$entry->scoreboardId = $lineNumber;
			$entry->objectiveName = 'dummy';
			$entry->score = $lineNumber;
			$entry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
			$entry->customName = $this->lines[$lineNumber];
			$entries[] = $entry;
		}

		if(count($entries) > 0){
			if(!$this->recentlySet){
				$this->player->getNetworkSession()->sendDataPacket(SetScorePacket::create(
					SetScorePacket::TYPE_REMOVE,
					$entries
				));
			}
			$this->player->getNetworkSession()->sendDataPacket(SetScorePacket::create(
				SetScorePacket::TYPE_CHANGE,
				$entries
			));
		}

		$this->recentlySet = false;
		$this->dirtyLines = [];
	}

	public function __destruct(){
		if($this->player->isConnected()){
			$this->remove();
		}
	}

	abstract protected function title() : string;

	abstract public function onUpdate() : void;
}

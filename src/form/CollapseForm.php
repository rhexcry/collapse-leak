<?php

declare(strict_types=1);

namespace collapse\form;

use collapse\player\CollapsePlayer;
use pocketmine\form\Form;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use function is_array;

abstract class CollapseForm implements Form{

	protected array $data = [];

	public function __construct(
		private ?\Closure $callable
	){}

	public function getCallable() : ?\Closure{
		return $this->callable;
	}

	public function setCallable(?\Closure $callable) : void{
		$this->callable = $callable;
	}

	public function handleResponse(Player $player, $data) : void{
		$this->processData($data);
		$callable = $this->getCallable();
		if($callable !== null){
			$callable($player, $data);
		}
	}

	public function processData(&$data) : void{
	}

	public function jsonSerialize() : array{
		return $this->data;
	}

	private function recursiveProcessForPlayer(CollapsePlayer $player, array $data) : array{
		$translator = $player->getProfile()->getTranslator();
		foreach($data as $key => $value){
			if($value instanceof Translatable){
				$data[$key] = $translator->translate($value);
			}elseif(is_array($value)){
				$data[$key] = $this->recursiveProcessForPlayer($player, $value);
			}
		}
		return $data;
	}

	final public function processForPlayer(CollapsePlayer $player) : void{
		$this->data = $this->recursiveProcessForPlayer($player, $this->data);
	}
}

<?php

declare(strict_types=1);

namespace collapse\game\teams;

use collapse\game\Game;
use collapse\game\teams\event\PlayerJoinTeamEvent;
use collapse\player\CollapsePlayer;
use pocketmine\block\utils\DyeColor;
use pocketmine\color\Color;
use pocketmine\lang\Translatable;

final class Team{

	/** @var CollapsePlayer[] */
	private array $players = [];

	/** @var (CollapsePlayer|null)[] */
	private array $playedPlayers = [];

	private ?Game $game = null;

	public function __construct(
		private readonly string $id,
		private readonly Translatable $name,
		private readonly string $color,
		private readonly Color $armorColor,
		private readonly DyeColor $dyeColor
	){}

	public function getId() : string{
		return $this->id;
	}

	public function getName() : Translatable{
		return $this->name;
	}

	public function getColor() : string{
		return $this->color;
	}

	public function getArmorColor() : Color{
		return $this->armorColor;
	}

	public function getDyeColor() : DyeColor{
		return $this->dyeColor;
	}

	public function getPlayers() : array{
		return $this->players;
	}

	public function addPlayer(CollapsePlayer $player) : void{
		$this->players[$player->getName()] = $player;
		(new PlayerJoinTeamEvent($this, $player))->call();
	}

	public function removePlayer(CollapsePlayer $player) : void{
		unset($this->players[$player->getName()]);
	}

	public function initPlayedPlayers() : void{
		foreach($this->players as $player){
			$this->playedPlayers[$player->getXuid()] = $player;
		}
	}

	public function getPlayedPlayers() : array{
		return $this->playedPlayers;
	}

	public function setGame(Game $game) : void{
		$this->game = $game;
	}

	public function getGame() : ?Game{
		return $this->game;
	}
}

<?php

declare(strict_types=1);

namespace collapse\game\teams;

use collapse\i18n\CollapseTranslationFactory;
use pocketmine\block\utils\DyeColor;
use pocketmine\color\Color;
use pocketmine\utils\TextFormat;
use function array_map;
use function array_slice;

final class Teams{

	public const string TEAM_RED = 'red';
	public const string TEAM_BLUE = 'blue';

	/** @var Team[] */
	private static ?array $teams = null;

	private function __construct(){}

	private static function map(Team $team) : void{
		self::$teams[$team->getId()] = $team;
	}

	public static function register() : void{
		if(self::$teams !== null){
			throw new \RuntimeException('Teams already initialized');
		}
		self::map(new Team(self::TEAM_RED, CollapseTranslationFactory::team_red_name(), TextFormat::RED, new Color(255, 0, 0), DyeColor::RED));
		self::map(new Team(self::TEAM_BLUE, CollapseTranslationFactory::team_blue_name(), TextFormat::BLUE, new Color(0, 0, 255), DyeColor::BLUE));
	}

	/**
	 * @return Team[]
	 */
	public static function create(int $count) : array{
		if($count < 1){
			throw new \InvalidArgumentException('Invalid team count: ' . $count);
		}
		if(self::$teams === null){
			self::register();
		}
		return array_map(fn(Team $team) : Team => clone $team, array_slice(self::$teams, 0, $count, true));
	}
}

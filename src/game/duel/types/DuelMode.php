<?php

declare(strict_types=1);

namespace collapse\game\duel\types;

use collapse\game\duel\Duel;
use collapse\game\duel\DuelConfig;
use collapse\game\duel\DuelWorldManager;
use collapse\game\duel\modes\BuildUHC;
use collapse\game\duel\modes\fireball\FireballFight;
use collapse\game\duel\modes\GApple;
use collapse\game\duel\modes\NoDebuff;
use collapse\game\kit\Kit;
use collapse\game\teams\TeamManager;
use collapse\Practice;
use pocketmine\utils\TextFormat;
use function array_filter;

enum DuelMode : string{

	case NoDebuff = 'no_debuff';
	case FireballFight = 'fireball_fight';
	case BuildUHC = 'builduhc';
	//case Sumo = 'sumo';
	case GApple = 'gapple';
	//case SkyWars = 'skywars';

	public function toDisplayName() : string{
		return match($this){
			self::NoDebuff => TextFormat::RED . 'No Debuff',
			self::FireballFight => TextFormat::GOLD . 'Fireball Fight',
			self::GApple => TextFormat::YELLOW . 'GApple',
			//self::Sumo => TextFormat::GREEN . 'Sumo',
			self::BuildUHC => TextFormat::BLUE . 'BuildUHC',
			//self::SkyWars => TextFormat::WHITE . 'SkyWars',
		};
	}

	public function toKit() : Kit{
		return match($this){
			self::NoDebuff => Kit::NoDebuff,
			self::FireballFight => Kit::FireballFight,
			self::GApple => Kit::GApple,
			//self::Sumo => Kit::Sumo,
			self::BuildUHC => Kit::BuildUHC,
			//self::SkyWars => Kit::SkyWars,
		};
	}

	public function isNoClientPredictionsOnStart() : bool{
		return $this !== self::NoDebuff;
	}

	public function isUsingTeamColor() : bool{
		return $this === self::FireballFight /*|| $this === self::SkyWars*/;
	}

	public function hasBlockUpdates() : bool{
		return $this === self::BuildUHC;
	}

	public function isRanked() : bool{
		return match($this){
			self::NoDebuff, self::FireballFight/*, self::Sumo*/, self::BuildUHC => true,
			default => false
		};
	}

	/**
	 * @return DuelMode[]
	 */
	public static function ranked() : array{
		return array_filter(DuelMode::cases(), static fn(DuelMode $mode) : bool => $mode->isRanked());
	}

	public function create(int $id, Practice $plugin, DuelConfig $config, DuelType $type, DuelWorldManager $worldManager, TeamManager $teamManager) : Duel{
		return match($this){
			self::NoDebuff => new NoDebuff($id, $plugin, $config, $type, $worldManager, $teamManager),
			self::FireballFight => new FireballFight($id, $plugin, $config, $type, $worldManager, $teamManager),
			self::GApple => new GApple($id, $plugin, $config, $type, $worldManager, $teamManager),
			//self::Sumo => new Sumo($id, $plugin, $config, $type, $worldManager, $teamManager),
			self::BuildUHC => new BuildUHC($id, $plugin, $config, $type, $worldManager, $teamManager),
			//self::SkyWars => new SkyWars($id, $plugin, $config, $type, $worldManager, $teamManager)
		};
	}

	public function toTexture() : string{
		return match($this){
			self::NoDebuff => 'nodebuff',
			self::FireballFight => 'fireballfight',
			//self::Sumo => 'sumo',
			self::GApple => 'gapple',
			self::BuildUHC => 'builduhc',
			//self::SkyWars => 'skywars',
		};
	}
}

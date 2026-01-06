<?php

declare(strict_types=1);

namespace collapse\cosmetics\capes;

use collapse\player\CollapsePlayer;
use collapse\player\profile\Profile;
use collapse\player\rank\Rank;
use function array_filter;
use function array_map;
use function str_replace;

enum Cape : string{

	case Fifteenth_Anniversary = CapeImages::FIFTEENTH_ANNIVERSARY;
	case Birthday = CapeImages::BIRTHDAY;
	case Cherry_Blossom = CapeImages::CHERRY_BLOSSOM;
	case Cobalt = CapeImages::COBALT;
	case Common = CapeImages::COMMON;
	case dB = CapeImages::dB;
	case Followers = CapeImages::FOLLOWERS;
	case Founders = CapeImages::FOUNDERS;
	case Home = CapeImages::HOME;
	case MCC_15th_Year = CapeImages::MCC_15TH_YEAR;
	case Menace = CapeImages::MENACE;
	case Migrator = CapeImages::MIGRATOR;
	case Millionth_Customer = CapeImages::MILLIONTH_CUSTOMER;
	case MineCon_2011 = CapeImages::MINECON_2011;
	case MineCon_2012 = CapeImages::MINECON_2012;
	case MineCon_2013 = CapeImages::MINECON_2013;
	case MineCon_2015 = CapeImages::MINECON_2015;
	case MineCon_2016 = CapeImages::MINECON_2016;
	case Minecraft_Experience = CapeImages::MINECRAFT_EXPERIENCE;
	case Mojang = CapeImages::MOJANG;
	case Mojang_Classic = CapeImages::MOJANG_CLASSIC;
	case Mojang_Office = CapeImages::MOJANG_OFFICE;
	case Mojang_Studios = CapeImages::MOJANG_STUDIOS;
	case Mojira_Moderator = CapeImages::MOJIRA_MODERATOR;
	case Oxeye = CapeImages::OXEYE;
	case Pan = CapeImages::PAN;
	case Prismarine = CapeImages::PRISMARINE;
	case Purple_Heart = CapeImages::PURPLE_HEART;
	case Realms_Mapmaker = CapeImages::REALMS_MAPMAKER;
	case Scrolls = CapeImages::SCROLLS;
	case Snowman = CapeImages::SNOWMAN;
	case Spade = CapeImages::SPADE;
	case Test = CapeImages::TEST;
	case Translator = CapeImages::TRANSLATOR;
	case Translator_Chinese = CapeImages::TRANSLATOR_CHINESE;
	case Translator_Japanese = CapeImages::TRANSLATOR_JAPANESE;
	case Turtle = CapeImages::TURTLE;
	case Valentine = CapeImages::VALENTINE;
	case Vanilla = CapeImages::VANILLA;
	case Yearn = CapeImages::YEARN;

	public function toDisplayName() : string{
		return str_replace('_', ' ', match ($this) {
			self::Fifteenth_Anniversary => '15th Anniversary',
			self::Birthday => 'Birthday',
			self::Cherry_Blossom => 'Cherry Blossom',
			self::Cobalt => 'Cobalt',
			self::Common => 'Common',
			self::dB => 'dB',
			self::Followers => 'Follower\'s',
			self::Founders => 'Founder\'s',
			self::Home => 'Home',
			self::MCC_15th_Year => 'MCC 15th Year',
			self::Menace => 'Menace',
			self::Migrator => 'Migrator',
			self::Millionth_Customer => 'Millionth Customer',
			self::MineCon_2011 => 'MineCon 2011',
			self::MineCon_2012 => 'MineCon 2012',
			self::MineCon_2013 => 'MineCon 2013',
			self::MineCon_2015 => 'MineCon 2015',
			self::MineCon_2016 => 'MineCon 2016',
			self::Minecraft_Experience => 'Minecraft Experience',
			self::Mojang => 'Mojang',
			self::Mojang_Classic => 'Mojang Classic',
			self::Mojang_Office => 'Mojang Office',
			self::Mojang_Studios => 'Mojang Studios',
			self::Mojira_Moderator => 'Mojira Moderator',
			self::Oxeye => 'Oxeye',
			self::Pan => 'Pan',
			self::Prismarine => 'Prismarine',
			self::Purple_Heart => 'Purple Heart',
			self::Realms_Mapmaker => 'Realms Mapmaker',
			self::Scrolls => 'Scrolls',
			self::Snowman => 'Snowman',
			self::Spade => 'Spade',
			self::Test => 'Test',
			self::Translator => 'Translator',
			self::Translator_Chinese => 'Chinese Translator',
			self::Translator_Japanese => 'Japanese Translator',
			self::Turtle => 'Turtle',
			self::Valentine => 'Valentine',
			self::Vanilla => 'Vanilla',
			self::Yearn => 'Yearn',
			default => $this->name
		});
	}

	public function getRank() : ?Rank{
		return match ($this) {
			self::Common,
			self::Home,
			self::Vanilla,
			self::Birthday,
			self::Valentine,
			self::Snowman,
			self::Turtle,
			self::Cherry_Blossom,
			self::Prismarine,
			self::Cobalt,
			self::Purple_Heart,
			self::Spade,
			self::Oxeye,
			self::Pan,
			self::Yearn => Rank::BLAZING,

			self::dB,
			self::Migrator,
			self::Mojang_Classic,
			self::Scrolls,
			self::Test,
			self::Mojira_Moderator,
			self::Translator,
			self::Translator_Chinese,
			self::Translator_Japanese,
			self::Realms_Mapmaker,
			self::Followers,
			self::Fifteenth_Anniversary => Rank::LUMINOUS,

			self::Founders,
			self::Mojang,
			self::Mojang_Studios,
			self::Mojang_Office,
			self::MineCon_2011,
			self::MineCon_2012,
			self::MineCon_2013,
			self::MineCon_2015,
			self::MineCon_2016,
			self::Millionth_Customer,
			self::Minecraft_Experience,
			self::MCC_15th_Year,
			self::Menace => Rank::ETHEREUM,

			default => null
		};
	}

	public function getPrice() : ?int{
		return match ($this) {
			self::Common,
			self::Home,
			self::Vanilla,
			self::Birthday,
			self::Valentine,
			self::Snowman,
			self::Turtle,
			self::Cherry_Blossom,
			self::Prismarine,
			self::Cobalt,
			self::Purple_Heart,
			self::Spade,
			self::Oxeye,
			self::Pan,
			self::Yearn => 10_000,

			self::dB,
			self::Migrator,
			self::Mojang_Classic,
			self::Scrolls,
			self::Test,
			self::Mojira_Moderator,
			self::Translator,
			self::Translator_Chinese,
			self::Translator_Japanese,
			self::Realms_Mapmaker,
			self::Followers,
			self::Fifteenth_Anniversary => 15_000,

			self::Founders,
			self::Mojang,
			self::Mojang_Studios,
			self::Mojang_Office,
			self::MineCon_2011,
			self::MineCon_2012,
			self::MineCon_2013,
			self::MineCon_2015,
			self::MineCon_2016,
			self::Millionth_Customer,
			self::Minecraft_Experience,
			self::MCC_15th_Year,
			self::Menace => 20_000,

			default => null
		};
	}

	public function toImage() : string{
		return $this->value;
	}

	public function canUse(Profile $profile) : bool{
		if($this->getRank() !== null &&
			$profile->getRank()->getPriority() >= $this->getRank()->getPriority()){
			return true;
		}

		return $profile->hasPurchasedCape($this);
	}

	public static function getAvailableCapes(CollapsePlayer $player) : array{
		$availableCapes = [];

		$playerRank = $player->getProfile()->getRank();
		$playerRankPriority = $playerRank->getPriority();

		$purchasedCapes = array_filter(
			array_map(static function(string $id) : ?Cape{
				return Cape::tryFrom($id);
			},
				$player->getProfile()->getPurchasedCapes()
			),
			static fn(?Cape $cape) : bool => $cape !== null);

		foreach(self::cases() as $cape){
			$capeRank = $cape->getRank();

			if($capeRank !== null && $playerRankPriority >= $capeRank->getPriority()){
				$availableCapes[] = $cape;
			}
		}

		foreach($purchasedCapes as $purchasedCape){
			if(!in_array($purchasedCape, $availableCapes, true)){
				$availableCapes[] = $purchasedCape;
			}
		}

		return $availableCapes;
	}
}

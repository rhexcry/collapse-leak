<?php

declare(strict_types=1);

namespace collapse\player\rank;

use collapse\resourcepack\Font;
use pocketmine\utils\TextFormat;
use function array_column;

enum Rank : string{

	case DEFAULT = 'default';
	case YONKO = 'yonko';
	case NECESSARY = 'necessary';
	case BLAZING = 'blazing';
	case LUMINOUS = 'luminous';
	case ETHEREUM = 'ethereum';
	case ARCANE = 'arcane';
	case CELESTIAL = 'celestial';
	case MEDIA = 'media';
	case FAMOUS = 'famous';
	case MODERATOR = 'moderator';
	case ADMIN = 'admin';
	case OWNER = 'owner';

	public function getPriority() : int{
		return match($this){
			self::DEFAULT => 0,
			self::YONKO => 1,
			self::NECESSARY => 2,
			self::BLAZING => 3,
			self::LUMINOUS => 4,
			self::ETHEREUM => 5,
			self::ARCANE => 6,
			self::CELESTIAL => 7,
			self::FAMOUS => 8,
			self::MEDIA => 9,
			self::MODERATOR => 10,
			self::ADMIN => 11,
			self::OWNER => 12,
		};
	}

	public function isStaffRank() : bool{
		return $this->getPriority() >= Rank::MODERATOR->getPriority();
	}

	public function isModeratorRank() : bool{
		return $this->getPriority() === Rank::MODERATOR->getPriority();
	}

	public function toColor() : string{
		return match($this){
			self::DEFAULT => TextFormat::GRAY,
			self::YONKO => TextFormat::MINECOIN_GOLD,
			self::NECESSARY => TextFormat::GREEN,
			self::BLAZING => TextFormat::GOLD,
			self::LUMINOUS => TextFormat::BLUE,
			self::ETHEREUM => TextFormat::DARK_PURPLE,
			self::ARCANE => TextFormat::RED,
			self::CELESTIAL => TextFormat::LIGHT_PURPLE,
			self::MEDIA => TextFormat::DARK_BLUE,
			self::FAMOUS => TextFormat::MATERIAL_AMETHYST,
			self::MODERATOR => TextFormat::AQUA,
			self::ADMIN => TextFormat::DARK_AQUA,
			self::OWNER => TextFormat::MATERIAL_REDSTONE
		};
	}

	public function toDisplayName() : string{
		return $this->toColor() . match($this){
			self::DEFAULT => 'Player',
			self::YONKO => 'Yonko',
			self::NECESSARY => 'Necessary',
			self::BLAZING => 'Blazing',
			self::LUMINOUS => 'Luminous',
			self::ETHEREUM => 'Ethereum',
			self::ARCANE => 'Arcane',
			self::CELESTIAL => 'Celestial',
			self::MEDIA => 'Media',
			self::FAMOUS => 'Famous',
			self::MODERATOR => 'Moderator',
			self::ADMIN => 'Admin',
			self::OWNER => 'Owner',
		};
	}

	public function toFont() : string{
		return match($this){
			self::DEFAULT => '',
			self::YONKO => Font::RANK_YONKO,
			self::NECESSARY => Font::RANK_NECESSARY,
			self::BLAZING => Font::RANK_BLAZING,
			self::LUMINOUS => Font::RANK_LUMINOUS,
			self::ETHEREUM => Font::RANK_ETHEREUM,
			self::ARCANE => Font::RANK_ARCANE,
			self::CELESTIAL => Font::RANK_CELESTIAL,
			self::MEDIA => Font::RANK_MEDIA,
			self::FAMOUS => Font::RANK_FAMOUS,
			self::MODERATOR => Font::RANK_MODERATOR,
			self::ADMIN => Font::RANK_ADMIN,
			self::OWNER => Font::RANK_OWNER
		};
	}

	public function toChatFormat() : string{
		return match($this){
			self::DEFAULT => '&7{nickname} &7» &7{message}',
			default => '&l' . $this->toFont() . ' &r&f&l{nickname} &r&b» &f{message}'
		};
	}

	public function toConsoleChatFormat() : string{
		return match($this){
			self::DEFAULT => '&7{nickname}&f: &7{message}',
			default => '&l' . $this->toDisplayName() . ' &r&f{nickname}&f: {message}'
		};
	}

	public function getPermissions() : array{
		return match($this){
			default => [],
			self::OWNER => ['*'],
		};
	}

	public static function values() : array{
		return array_column(self::cases(), 'value');
	}
}

<?php

declare(strict_types=1);

namespace collapse\cosmetics\tags;

use collapse\player\CollapsePlayer;
use collapse\player\profile\Profile;
use collapse\player\rank\Rank;
use pocketmine\utils\TextFormat;
use function array_filter;
use function array_map;

enum ChatTag : string{

	case Unbeatable = 'unbeatable';
	case BestWW = 'best_ww';
	case GOAT = 'goat';
	case Cripple = 'cripple';
	case Clicking = 'Clicking';
	case Autoclicking = 'autoclicking';
	case Blatant = 'blatant';

	public function toDisplayName() : string{
		return match ($this) {
				self::Unbeatable => TextFormat::BOLD . TextFormat::DARK_AQUA . 'UNBEATABLE',
				self::BestWW => TextFormat::BOLD . TextFormat::DARK_RED . 'BestWW',
				self::GOAT => TextFormat::BOLD . TextFormat::AQUA . 'GOAT',
				self::Cripple => TextFormat::AQUA . '♿',
				self::Clicking => TextFormat::BOLD . TextFormat::GREEN . 'CLICKING',
				self::Autoclicking => TextFormat::BOLD . TextFormat::RED . 'AUTOCLICKING',
				self::Blatant => TextFormat::BOLD . TextFormat::BLUE . 'BLATANT'
			} . TextFormat::RESET;
	}

	public function getRank() : ?Rank{
		return match ($this) {
			self::Unbeatable, self::BestWW => Rank::LUMINOUS,
			self::GOAT, self::Cripple, self::Clicking, self::Autoclicking, self::Blatant => Rank::ETHEREUM,
			default => null
		};
	}

	public function getPrice() : ?int{
		return match ($this) {
			self::Unbeatable, self::BestWW => 5000,
			self::GOAT, self::Cripple, self::Clicking, self::Autoclicking, self::Blatant => 12500,
			default => null
		};
	}

	public function canUse(Profile $profile) : bool{
		if($this->getRank() !== null &&
			$profile->getRank()->getPriority() >= $this->getRank()->getPriority()){
			return true;
		}

		return $profile->hasPurchasedChatTag($this);
	}

	/**
	 * @return ChatTag[]
	 */
	public static function getAvailableChatTags(CollapsePlayer $player) : array{
		$availableTags = [];

		$playerRank = $player->getProfile()->getRank();
		$playerRankPriority = $playerRank->getPriority();

		$purchasedTags = array_filter(
			array_map(static function(string $id) : ?ChatTag{
				return ChatTag::tryFrom($id);
			},
				$player->getProfile()->getPurchasedChatTags()
			),
			static fn(?ChatTag $tag) : bool => $tag !== null);

		foreach(self::cases() as $tag){
			$tagRank = $tag->getRank();

			if($tagRank !== null && $playerRankPriority >= $tagRank->getPriority()){
				$availableTags[] = $tag;
			}
		}

		foreach($purchasedTags as $purchasedTag){
			if(!in_array($purchasedTag, $availableTags, true)){
				$availableTags[] = $purchasedTag;
			}
		}

		return $availableTags;
	}
}

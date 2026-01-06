<?php

declare(strict_types=1);

namespace collapse\cosmetics\effects\death;

use collapse\player\CollapsePlayer;
use collapse\player\profile\Profile;
use collapse\player\rank\Rank;
use function array_filter;
use function array_map;
use function array_merge;

enum DeathEffectType : string{

	case Explode = 'explode';
	case DeadBody = 'dead_body';

	public function toDisplayName() : string{
		return $this->name;
	}

	public function create(CollapsePlayer $player, CollapsePlayer $killer) : void{
		match ($this) {
			self::Explode => new ExplodeDeathEffect($player, $killer),
			self::DeadBody => new DeadBodyDeathEffect($player, $killer),
		};
	}

	public function getRank() : ?Rank{
		return match ($this) {
			self::Explode,
			self::DeadBody => Rank::BLAZING,

			default => null
		};
	}

	public function getPrice() : ?int{
		return match ($this) {
			self::Explode => 5000,
			default => null
		};
	}

	public function canUse(Profile $profile) : bool{
		if($this->getRank() !== null && $profile->getRank()->getPriority() < $this->getRank()->getPriority()){
			return false;
		}
		return $profile->hasPurchasedDeathEffect($this) || $this->getPrice() === null;
	}

	/**
	 * @return DeathEffectType[]
	 */
	public static function getAvailableDeathEffects(CollapsePlayer $player) : array{
		return array_merge(
			array_filter(self::cases(), static fn(DeathEffectType $deathEffect) : bool =>
				$deathEffect->getPrice() === null &&
				$deathEffect->getRank() !== null &&
				$player->getProfile()->getRank()->getPriority() >= $deathEffect->getRank()->getPriority()
			),
			array_filter(array_map(static function(string $id) : ?DeathEffectType{
				return DeathEffectType::tryFrom($id);
			}, $player->getProfile()->getPurchasedDeathEffects()), static fn(?DeathEffectType $deathEffect) : bool => $deathEffect !== null)
		);
	}
}

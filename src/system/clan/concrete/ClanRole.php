<?php

declare(strict_types=1);

namespace collapse\system\clan\concrete;

enum ClanRole : string{

	case LEADER = 'leader';
	case CO_LEADER = 'co_leader';
	case OFFICER = 'officer';
	case MEMBER = 'member';

	public function getDisplayName() : string{
		return match($this){
			self::LEADER => "Leader",
			self::CO_LEADER => "Co-Leader",
			self::OFFICER => "Officer",
			self::MEMBER => "Member"
		};
	}

	public function canInvite() : bool{
		return match($this){
			self::LEADER, self::CO_LEADER, self::OFFICER => true,
			default => false
		};
	}

	public function canKick(ClanRole $targetRole) : bool{
		return match($this){
			self::LEADER, self::CO_LEADER => true,
			self::OFFICER => $targetRole !== self::CO_LEADER && $targetRole !== self::LEADER,
			default => false
		};
	}

	public function canPromoteDemote(ClanRole $targetRole) : bool{
		return match($this){
			self::LEADER => $targetRole !== self::LEADER,
			self::CO_LEADER => $targetRole === self::OFFICER || $targetRole === self::MEMBER,
			default => false
		};
	}

	public function canWithdrawFunds() : bool{
		return match($this){
			self::LEADER, self::CO_LEADER => true,
			default => false
		};
	}

	public function canUpgradeClan() : bool{
		return match($this){
			self::LEADER, self::CO_LEADER => true,
			default => false
		};
	}

	public function canStartWar() : bool{
		return match($this){
			self::LEADER, self::CO_LEADER => true,
			default => false
		};
	}
}

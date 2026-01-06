<?php

declare(strict_types=1);

namespace collapse\system\clan\concrete;

final class ClanMember{

	public function __construct(
		private readonly string $xuid,
		private ClanRole $role,
		private int $kills = 0
	){
	}

	public function getXuid() : string{
		return $this->xuid;
	}

	public function getRole() : ClanRole{
		return $this->role;
	}

	public function setRole(ClanRole $role) : void{
		$this->role = $role;
	}

	public function getKills() : int{
		return $this->kills;
	}

	public function addKill() : void{
		$this->kills++;
	}
}

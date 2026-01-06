<?php

declare(strict_types=1);

namespace collapse\system\clan\concrete;

use collapse\mongo\MongoUtils;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\resourcepack\Font;
use collapse\system\clan\ClanConstants;
use MongoDB\BSON\ObjectId;
use MongoDB\Model\BSONDocument;
use function array_filter;
use function array_map;
use function count;
use function strtolower;
use function time;

final class Clan{

	private array $members = [];

	public static function fromBsonDocument(BSONDocument $document) : self{
		$data = MongoUtils::bsonDocumentToArray($document);
		$clan = new self($data);

		$clan->members = array_map(
			fn(array $member) => new ClanMember($member['xuid'], ClanRole::from($member['role']), $member['kills']),
			$data['members'] ?? []
		);

		return $clan;
	}

	public static function create(CollapsePlayer $leader, string $name, string $tag) : self{
		return new self([
			'name' => $name,
			'lowerName' => strtolower($name),
			'tag' => $tag,
			'lowerTag' => strtolower($tag),
			'createdAt' => time(),
			'leaderXuid' => $leader->getProfile()->getXuid(),
			'members' => [
				[
					'xuid' => $leader->getProfile()->getXuid(),
					'role' => ClanRole::LEADER->value,
					'kills' => 0
				]
			],
			'kills' => 0,
			'deaths' => 0,
			'wins' => 0,
			'losses' => 0,
			'treasury' => 0,
			'upgrades' => [
				'kill_multiplier' => 1.0,
				'slots' => ClanConstants::DEFAULT_SLOTS
			]
		]);
	}

	private function __construct(
		private array $values
	){
	}

	public function onInsert(?ObjectId $id) : void{
		if($id === null){
			return;
		}

		$this->values['_id'] = $id;
	}

	public function getId() : ?ObjectId{
		return $this->values['_id'] ?? null;
	}

	public function getName() : string{
		return $this->values['name'];
	}

	public function getTag() : string{
		return $this->values['tag'];
	}

	public function getWins() : int{
		return $this->values['wins'];
	}

	public function getLosses() : int{
		return $this->values['losses'];
	}

	public function getLeaderXuid() : string{
		return $this->values['leaderXuid'];
	}

	public function getTotalKills() : int{
		return $this->values['kills'];
	}

	public function getTotalDeaths() : int{
		return $this->values['deaths'];
	}

	public function getKDRatio() : float{
		return $this->values['deaths'] === 0 ? $this->values['kills'] : $this->values['kills'] / $this->values['deaths'];
	}

	public function getTreasury() : int{
		return $this->values['treasury'];
	}

	public function addToTreasury(int $amount) : void{
		$this->values['treasury'] += $amount;
		$this->save();
	}

	public function withdrawFromTreasury(int $amount) : void{
		if($this->values['treasury'] < $amount){
			return;
		}

		$this->values['treasury'] -= $amount;
		$this->save();
	}

	public function getKillMultiplier() : float{
		return $this->values['upgrades']['kill_multiplier'];
	}

	public function upgradeKillMultiplier(float $newMultiplier, int $cost) : void{
		if($this->values['treasury'] < $cost || $newMultiplier <= $this->values['upgrades']['kill_multiplier']){
			return;
		}

		$this->values['upgrades']['kill_multiplier'] = $newMultiplier;
		$this->values['treasury'] -= $cost;
		$this->save();
	}

	public function getSlots() : int{
		return $this->values['upgrades']['slots'];
	}

	public function upgradeSlots(int $newSlots, int $cost) : void{
		if($this->values['treasury'] < $cost || $newSlots <= $this->values['upgrades']['slots']){
			return;
		}

		$this->values['upgrades']['slots'] = $newSlots;
		$this->values['treasury'] -= $cost;
		$this->save();
	}

	/**
	 * @return ClanMember[]
	 */
	public function getMembers() : array{
		return $this->members;
	}

	public function getFontedTag() : string{
		return '  ' . Font::bold($this->getTag());
	}

	public function addMember(CollapsePlayer $player, ClanRole $role = ClanRole::MEMBER) : void{
		if($this->getMemberCount() >= $this->getSlots()){
			return;
		}

		$xuid = $player->getProfile()->getXuid();
		if($this->getMemberByXuid($xuid) !== null){
			return;
		}

		$this->members[] = new ClanMember($xuid, $role);
		$this->values['members'][] = [
			'xuid' => $xuid,
			'role' => $role->value,
			'kills' => 0
		];

		$player->getProfile()?->setClanId($this->getId());
		$player->getProfile()?->setClanRole($role);

		$this->save();
	}

	public function removeMember(string $xuid) : void{
		$this->members = array_filter($this->members, fn(ClanMember $member) => $member->getXuid() !== $xuid);
		$this->values['members'] = array_filter($this->values['members'], fn(array $member) => $member['xuid'] !== $xuid);

		if(($player = Practice::getPlayerByXuid($xuid)) !== null){
			$player->getProfile()?->setClanId(null);
			$player->getProfile()?->setClanRole(null);
		}

		$this->save();
	}

	public function promoteMember(string $xuid) : void{
		$member = $this->getMemberByXuid($xuid);
		if($member === null)
			return;

		$newRole = match($member->getRole()){
			ClanRole::MEMBER => ClanRole::OFFICER,
			ClanRole::OFFICER => ClanRole::CO_LEADER,
			default => $member->getRole()
		};

		if($newRole === $member->getRole())
			return;

		$member->setRole($newRole);
		$this->updateMemberRoleInData($xuid, $newRole);
		$this->save();
	}

	public function demoteMember(string $xuid) : void{
		$member = $this->getMemberByXuid($xuid);
		if($member === null)
			return;

		$newRole = match($member->getRole()){
			ClanRole::CO_LEADER => ClanRole::OFFICER,
			ClanRole::OFFICER => ClanRole::MEMBER,
			default => $member->getRole()
		};

		if($newRole === $member->getRole())
			return;

		$member->setRole($newRole);
		$this->updateMemberRoleInData($xuid, $newRole);
		$this->save();
	}

	private function updateMemberRoleInData(string $xuid, ClanRole $role) : void{
		foreach($this->values['members'] as &$member){
			if($member['xuid'] === $xuid){
				$member['role'] = $role->value;
				break;
			}
		}
	}

	public function getMemberByXuid(string $xuid) : ?ClanMember{
		foreach($this->members as $member){
			if($member->getXuid() === $xuid){
				return $member;
			}
		}
		return null;
	}

	public function getMemberCount() : int{
		return count($this->members);
	}

	/**
	 * @return ClanMember[]
	 */
	public function getOnlineMembers() : array{
		$members = [];

		foreach(Practice::onlinePlayers() as $player){
			if($this->getMemberByXuid($player->getProfile()->getXuid()) !== null){
				$members[] = $player;
			}
		}

		return $members;
	}

	public function onMemberKill(CollapsePlayer $killer) : void{
		$xuid = $killer->getProfile()->getXuid();
		$member = $this->getMemberByXuid($xuid);

		if($member !== null){
			$member->addKill();
			$this->values['kills']++;

			foreach($this->values['members'] as &$m){
				if($m['xuid'] === $xuid){
					$m['kills']++;
					break;
				}
			}

			$this->save();
		}
	}

	public function onMemberDeath() : void{
		$this->values['deaths']++;
		$this->save();
	}

	public function export() : array{
		return $this->values;
	}

	public function save() : void{
		Practice::getInstance()->getClanManager()->saveClan($this);
	}
}

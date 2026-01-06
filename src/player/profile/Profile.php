<?php

declare(strict_types=1);

namespace collapse\player\profile;

use collapse\cosmetics\capes\Cape;
use collapse\cosmetics\capes\CapeChangeEvent;
use collapse\cosmetics\effects\death\DeathEffectChangeEvent;
use collapse\cosmetics\effects\death\DeathEffectType;
use collapse\cosmetics\potion\PotionColor;
use collapse\cosmetics\potion\PotionColorChangeEvent;
use collapse\cosmetics\tags\ChatTag;
use collapse\cosmetics\tags\ChatTagChangeEvent;
use collapse\game\duel\records\DuelRecord;
use collapse\game\duel\types\DuelMode;
use collapse\game\duel\types\DuelType;
use collapse\game\ffa\types\FreeForAllMode;
use collapse\game\kit\Kit;
use collapse\i18n\Translator;
use collapse\i18n\types\LanguageInterface;
use collapse\mongo\MongoUtils;
use collapse\player\CollapsePlayer;
use collapse\player\rank\Rank;
use collapse\player\settings\Setting;
use collapse\Practice;
use collapse\system\clan\concrete\Clan;
use collapse\system\clan\concrete\ClanRole;
use collapse\system\kiteditor\layout\KitLayout;
use collapse\wallet\currency\Currency;
use MongoDB\BSON\ObjectId;
use MongoDB\Model\BSONDocument;
use pocketmine\player\XboxLivePlayerInfo;
use function array_search;
use function array_sum;
use function count;
use function in_array;
use function round;
use function strtolower;
use function time;

final class Profile{

	private ?CollapsePlayer $player = null;

	private ?Rank $rank = null;

	private ?Translator $translator = null;

	private ?Cape $cape = null;
	private ?ChatTag $chatTag = null;
	private ?DeathEffectType $deathEffect = null;
	private ?PotionColor $potionColor = null;

	private ?ObjectId $clanId = null;
	private ?ClanRole $clanRole = null;

	private int $totalMp = 0;

	private array $issuedPunishments = [];
	private array $onlineMinutes = [];

	public static function fromBsonDocument(BSONDocument $document) : self{
		return new self(MongoUtils::bsonDocumentToArray($document));
	}

	public static function create(CollapsePlayer $player) : self{
		$playerInfo = $player->getPlayerInfo();
		if(!$playerInfo instanceof XboxLivePlayerInfo){
			throw new \InvalidArgumentException('Player must be authorized in Xbox');
		}
		return new self([
			'xuid' => $playerInfo->getXuid(),
			'playerName' => $player->getName(),
			'lowerCasePlayerName' => strtolower($player->getName()),
			'ip' => $player->getNetworkSession()->getIp(),
			'device_id' => $playerInfo->getExtraData()['DeviceId'],
			'device_model' => $playerInfo->getExtraData()['DeviceModel'],
			'device_os' => $playerInfo->getExtraData()['DeviceOS'],
			'input_mode' => $playerInfo->getExtraData()['CurrentInputMode'],
			'first_join_time' => time(),
			'game_version' => $playerInfo->getExtraData()['GameVersion']
		]);
	}

	private function __construct(
		private array $values
	){}

	public function setPlayer(CollapsePlayer $player) : void{
		if($this->player !== null){
			throw new \InvalidArgumentException('Profile already have player');
		}
		$this->player = $player;
	}

	public function getPlayer() : ?CollapsePlayer{
		return $this->player;
	}

	public function onInsert(ObjectId $insertedId) : void{
		if(isset($this->values['_id'])){
			throw new \InvalidArgumentException('Profile already have unique id');
		}
		$this->values['_id'] = $insertedId;
	}

	public function getId() : ?ObjectId{
		return $this->values['_id'];
	}

	public function getXuid() : string{
		return $this->values['xuid'];
	}

	public function getPlayerName() : string{
		return $this->values['playerName'];
	}

	public function getLowerCasePlayerName() : string{
		return $this->values['lowerCasePlayerName'];
	}

	public function getIp() : string{
		return $this->values['ip'];
	}

	public function getDeviceId() : string{
		return $this->values['device_id'];
	}

	public function getDeviceModel() : string{
		return $this->values['device_model'];
	}

	public function getDeviceOS() : int{
		return $this->values['device_os'];
	}

	public function getInputMode() : int{
		return $this->values['input_mode'];
	}

	public function getFirstJoinTime() : int{
		return $this->values['first_join_time'];
	}

	public function getGameVersion() : string{
		return $this->values['game_version'];
	}

	public function setRank(?Rank $rank) : void{
		$this->rank = $rank;
		$this->set('rank', $rank->value);
	}

	public function getRank() : Rank{
		return $this->rank ??= Rank::tryFrom($this->get('rank', Rank::DEFAULT->value));
	}

	public function getTranslator() : ?Translator{
		return $this->translator;
	}

	public function setTranslator(Translator $translator) : void{
		$this->translator = $translator;
	}

	public function setLanguage(LanguageInterface $language) : void{
		$this->set('language', $language->getName());
	}

	public function getLanguage() : ?string{
		return $this->get('language');
	}

	public function getSetting(Setting $setting) : bool{
		return $this->get('settings', [])[$setting->value] ?? $setting->isDefault();
	}

	public function setSetting(Setting $setting, bool $value) : void{
		$settings = $this->get('settings', []);
		$settings[$setting->value] = $value;
		$this->set('settings', $settings);
	}

	public function getCape() : ?Cape{
		$this->cape ??= Cape::tryFrom($this->get('cape', ''));
		if($this->cape !== null && !$this->cape->canUse($this)){
			$this->setChatTag(null);
			return null;
		}
		return $this->cape;
	}

	public function setCape(?Cape $cape) : void{
		if($cape === null){
			$this->remove('cape');
		}else{
			$this->set('cape', $cape->value);
		}
		if($this->cape !== $cape){
			(new CapeChangeEvent($this, $cape))->call();
		}
		$this->cape = $cape;
	}

	public function getPurchasedCapes() : array{
		return $this->get('purchased_capes', []);
	}

	public function addPurchasedCape(Cape $cape) : void{
		$purchasedCapes = $this->getPurchasedCapes();
		$purchasedCapes[] = $cape->value;
		$this->set('purchased_capes', $purchasedCapes);
	}

	public function removePurchasedCape(Cape $cape) : void{
		$purchasedCapes = $this->getPurchasedCapes();
		if(!in_array($cape, $purchasedCapes, true)){
			return;
		}
		unset($purchasedCapes[array_search($cape, $purchasedCapes, true)]);
		$this->set('purchased_capes', $purchasedCapes);
	}

	public function hasPurchasedCape(Cape $cape) : bool{
		return in_array($cape->value, $this->getPurchasedCapes(), true);
	}

	public function getChatTag() : ?ChatTag{
		$this->chatTag ??= ChatTag::tryFrom($this->get('chat_tag', ''));
		if($this->chatTag !== null && !$this->chatTag->canUse($this)){
			$this->setChatTag(null);
			return null;
		}
		return $this->chatTag;
	}

	public function setChatTag(?ChatTag $chatTag) : void{
		if($chatTag === null){
			$this->remove('chat_tag');
		}else{
			$this->set('chat_tag', $chatTag->value);
		}
		if($this->chatTag !== $chatTag){
			(new ChatTagChangeEvent($this, $chatTag))->call();
		}
		$this->chatTag = $chatTag;
	}

	/**
	 * @return string[]
	 * @see ChatTag
	 */
	public function getPurchasedChatTags() : array{
		return $this->get('purchased_chat_tags', []);
	}

	public function addPurchasedChatTag(ChatTag $chatTag) : void{
		$purchasedChatTags = $this->getPurchasedChatTags();
		$purchasedChatTags[] = $chatTag->value;
		$this->set('purchased_chat_tags', $purchasedChatTags);
	}

	public function removePurchasedChatTag(ChatTag $chatTag) : void{
		$purchasedChatTags = $this->getPurchasedChatTags();
		if(!in_array($chatTag, $purchasedChatTags, true)){
			return;
		}
		unset($purchasedChatTags[array_search($chatTag, $purchasedChatTags, true)]);
		$this->set('purchased_chat_tags', $purchasedChatTags);
	}

	public function hasPurchasedChatTag(ChatTag $chatTag) : bool{
		return in_array($chatTag->value, $this->getPurchasedChatTags(), true);
	}

	public function getDeathEffect() : ?DeathEffectType{
		$this->deathEffect ??= DeathEffectType::tryFrom($this->get('death_effect', ''));
		if($this->deathEffect !== null && !$this->deathEffect->canUse($this)){
			$this->setDeathEffect(null);
			return null;
		}
		return $this->deathEffect;
	}

	public function setDeathEffect(?DeathEffectType $deathEffect) : void{
		if($deathEffect === null){
			$this->remove('death_effect');
		}else{
			$this->set('death_effect', $deathEffect->value);
		}
		if($this->deathEffect !== $deathEffect){
			(new DeathEffectChangeEvent($this, $deathEffect))->call();
		}
		$this->deathEffect = $deathEffect;
	}

	/**
	 * @return string[]
	 * @see DeathEffectType
	 */
	public function getPurchasedDeathEffects() : array{
		return $this->get('purchased_death_effects', []);
	}

	public function addPurchasedDeathEffect(DeathEffectType $deathEffectType) : void{
		$purchasedDeathEffects = $this->getPurchasedDeathEffects();
		$purchasedDeathEffects[] = $deathEffectType->value;
		$this->set('purchased_death_effects', $purchasedDeathEffects);
	}

	public function removePurchasedDeathEffect(DeathEffectType $deathEffect) : void{
		$purchasedDeathEffects = $this->getPurchasedDeathEffects();
		if(!in_array($deathEffect, $purchasedDeathEffects, true)){
			return;
		}
		unset($purchasedDeathEffects[array_search($deathEffect, $purchasedDeathEffects, true)]);
		$this->set('purchased_death_effects', $purchasedDeathEffects);
	}

	public function hasPurchasedDeathEffect(DeathEffectType $deathEffect) : bool{
		return in_array($deathEffect->value, $this->getPurchasedDeathEffects(), true);
	}

	public function getPotionColor() : ?PotionColor{
		$this->potionColor ??= PotionColor::tryFrom($this->get('custom_potion_color', ''));
		if($this->potionColor !== null && !$this->potionColor->canUse($this)){
			$this->setPotionColor(null);
			return null;
		}
		return $this->potionColor;
	}

	public function setPotionColor(?PotionColor $potionColor) : void{
		$this->potionColor = $potionColor;
		if($potionColor === null){
			$this->remove('custom_potion_color');
		}else{
			$this->set('custom_potion_color', $potionColor->value);
		}
		(new PotionColorChangeEvent($this, $potionColor))->call();
	}

	public function setFreeForAllKills(int $value, ?FreeForAllMode $mode = null, ?int $subValue = null) : void{
		$this->set('free_for_all_kills', $value);
		if($mode !== null && $subValue !== null){
			$this->set('free_for_all_kills_' . $mode->value, $subValue);
		}
	}

	public function getFreeForAllKills(?FreeForAllMode $mode = null) : int{
		return $mode === null ? $this->get('free_for_all_kills', 0) : $this->get('free_for_all_kills_' . $mode->value, 0);
	}

	public function setFreeForAllDeaths(int $value, ?FreeForAllMode $mode = null, ?int $subValue = null) : void{
		$this->set('free_for_all_deaths', $value);
		if($mode !== null && $subValue !== null){
			$this->set('free_for_all_deaths_' . $mode->value, $subValue);
		}
	}

	public function getFreeForAllDeaths(?FreeForAllMode $mode = null) : int{
		return $mode === null ? $this->get('free_for_all_deaths', 0) : $this->get('free_for_all_deaths_' . $mode->value, 0);
	}

	public function getFreeForAllKillDeathRatio(?FreeForAllMode $mode = null) : float{
		$kills = $this->getFreeForAllKills($mode);
		$deaths = $this->getFreeForAllDeaths($mode);
		if($deaths === 0){
			return $kills > 0 ? $kills / 1.0 : 0.0;
		}
		return ($kills / $deaths);
	}

	public function setFreeForAllKillStreak(int $value, ?FreeForAllMode $mode = null, ?int $subValue = null) : void{
		$this->set('free_for_all_kill_streak', $value);
		if($mode !== null && $subValue !== null){
			$this->set('free_for_all_kill_streak_' . $mode->value, $subValue);
		}
	}

	public function getFreeForAllKillStreak(?FreeForAllMode $mode = null) : int{
		return $mode === null ? $this->get('free_for_all_kill_streak', 0) : $this->get('free_for_all_kill_streak_' . $mode->value, 0);
	}

	public function setFreeForAllBestKillStreak(int $value, ?FreeForAllMode $mode = null, ?int $subValue = null) : void{
		$this->set('free_for_all_best_kill_streak', $value);
		if($mode !== null && $subValue !== null){
			$this->set('free_for_all_best_kill_streak_' . $mode->value, $subValue);
		}
	}

	public function getFreeForAllBestKillStreak(?FreeForAllMode $mode = null) : int{
		return $mode === null ? $this->get('free_for_all_best_kill_streak', 0) : $this->get('free_for_all_best_kill_streak_' . $mode->value, 0);
	}

	public function onFreeForAllKill(FreeForAllMode $mode) : void{
		$this->setFreeForAllKills($this->getFreeForAllKills() + 1, $mode, $this->getFreeForAllKills($mode) + 1);
		$killStreak = $this->getFreeForAllKillStreak();
		$killStreakMode = $this->getFreeForAllKillStreak($mode);
		$this->setFreeForAllKillStreak(++$killStreak, $mode, ++$killStreakMode);
		$bestKillStreak = $this->getFreeForAllBestKillStreak();
		$bestKillStreakMode = $this->getFreeForAllBestKillStreak($mode);
		if($killStreak > $bestKillStreak){
			++$bestKillStreak;
		}
		if($killStreakMode > $bestKillStreakMode){
			++$bestKillStreakMode;
		}
		$this->setFreeForAllBestKillStreak($bestKillStreak, $mode, $bestKillStreakMode);
	}

	public function onFreeForAllDeath(FreeForAllMode $mode) : void{
		$this->setFreeForAllDeaths($this->getFreeForAllDeaths() + 1, $mode, $this->getFreeForAllDeaths($mode) + 1);
		$killStreak = $this->getFreeForAllKillStreak();
		if($killStreak > 0){
			$killStreakMode = $this->getFreeForAllKillStreak($mode);
			$this->setFreeForAllKillStreak($killStreak - $killStreakMode, $mode, 0);
		}
	}

	public function setDuelsWins(int $value, DuelType $type, ?DuelMode $mode = null, ?int $modeValue = null) : void{
		$this->set('duels_' . $type->value . '_wins', $value);
		if($mode !== null && $modeValue !== null){
			$this->set('duels_' . $type->value . '_wins_' . $mode->value, $modeValue);
		}
	}

	public function getDuelsWins(DuelType $type, ?DuelMode $mode = null) : int{
		return $mode === null ? $this->get('duels_' . $type->value . '_wins', 0) : $this->get('duels_' . $type->value . '_wins_' . $mode->value, 0);
	}

	public function setDuelsLosses(int $value, DuelType $type, ?DuelMode $mode = null, ?int $subValue = null) : void{
		$this->set('duels_' . $type->value . '_losses', $value);
		if($mode !== null && $subValue !== null){
			$this->set('duels_' . $type->value . '_losses_' . $mode->value, $subValue);
		}
	}

	public function getDuelsLosses(DuelType $type, ?DuelMode $mode = null) : int{
		return $mode === null ? $this->get('duels_' . $type->value . '_losses', 0) : $this->get('duels_' . $type->value . '_losses_' . $mode->value, 0);
	}

	public function setDuelsPlays(int $value, DuelType $type, ?DuelMode $mode = null, ?int $subValue = null) : void{
		$this->set('duels_' . $type->value . '_plays', $value);
		if($mode !== null && $subValue !== null){
			$this->set('duels_' . $type->value . '_plays_' . $mode->value, $subValue);
		}
	}

	public function getDuelsPlays(DuelType $type, ?DuelMode $mode = null) : int{
		return $mode === null ? $this->get('duels_' . $type->value . '_plays', 0) : $this->get('duels_' . $type->value . '_plays_' . $mode->value, 0);
	}

	public function getDuelsWinrate(DuelType $type, ?DuelMode $mode = null) : float{
		$wins = $this->getDuelsWins($type, $mode);
		$plays = $this->getDuelsPlays($type, $mode);
		if($wins === 0 || $plays === 0){
			return 0.0;
		}
		return ($wins / $plays) * 100;
	}

	public function setDuelsWinStreak(int $value, DuelType $type, ?DuelMode $mode = null, ?int $subValue = null) : void{
		$this->set('duels_' . $type->value . '_win_streak', $value);
		if($mode !== null && $subValue !== null){
			$this->set('duels_' . $type->value . '_win_streak' . $mode->value, $subValue);
		}
	}

	public function getDuelsWinStreak(DuelType $type, ?DuelMode $mode = null) : int{
		return $mode === null ? $this->get('duels_' . $type->value . '_win_streak', 0) : $this->get('duels_' . $type->value . '_win_streak' . $mode->value, 0);
	}

	public function setDuelsBestWinStreak(int $value, DuelType $type, ?DuelMode $mode = null, ?int $subValue = null) : void{
		$this->set('duels_' . $type->value . '_best_win_streak', $value);
		if($mode !== null && $subValue !== null){
			$this->set('duels_' . $type->value . '_best_win_streak' . $mode->value, $subValue);
		}
	}

	public function getDuelsBestWinStreak(DuelType $type, ?DuelMode $mode = null) : int{
		return $mode === null ? $this->get('duels_' . $type->value . '_best_win_streak', 0) : $this->get('duels_' . $type->value . '_best_win_streak' . $mode->value, 0);
	}

	public function setDuelsElo(DuelMode $mode, int $elo) : void{
		$this->set('duels_ranked_elo_' . $mode->value, $elo);
		if($elo > $this->getDuelsPeekElo($mode)){
			$this->setDuelsPeekElo($mode, $elo);
		}
	}

	public function onRecalculateGlobalElo() : void{
		$elo = [];
		foreach(DuelMode::ranked() as $mode){
			$elo[] = $this->getDuelsElo($mode);
		}
		$this->setDuelsGlobalElo((int) round(array_sum($elo) / count($elo)));
	}

	public function getDuelsElo(DuelMode $mode) : int{
		return $this->get('duels_ranked_elo_' . $mode->value, 1000);
	}

	public function setDuelsPeekElo(DuelMode $mode, int $elo) : void{
		$this->set('duels_ranked_peek_elo_' . $mode->value, $elo);
	}

	public function getDuelsPeekElo(DuelMode $mode) : int{
		return $this->get('duels_ranked_peek_elo_' . $mode->value, 1000);
	}

	public function setDuelsGlobalElo(int $elo) : void{
		$this->set('duels_ranked_global_elo', $elo);
	}

	public function getDuelsGlobalElo() : int{
		return $this->get('duels_ranked_global_elo', 1000);
	}

	public function onDuelMatch(ObjectId $id, DuelRecord $record) : void{
		$history = $this->get('duels_match_history');
		$history[] = [
			'id' => $id,
			'mode' => $record->getMode()->value,
			'type' => $record->getType()->value,
			'time' => $record->getTime(),
			'winners' => $record->getWinners(),
			'losers' => $record->getLosers(),
			'eloUpdates' => $record->getEloUpdates()
		];
		$this->set('duels_match_history', $history);
	}

	public function onDuelWin(DuelType $type, DuelMode $mode) : void{
		$this->setDuelsWins($this->getDuelsWins($type) + 1, $type, $mode, $this->getDuelsWins($type, $mode) + 1);
		$winStreak = $this->getDuelsWinStreak($type);
		$winStreakMode = $this->getDuelsWinStreak($type, $mode);
		$this->setDuelsWinStreak(++$winStreak, $type, $mode, ++$winStreakMode);
		$bestWinStreak = $this->getDuelsBestWinStreak($type);
		$bestWinStreakMode = $this->getDuelsBestWinStreak($type, $mode);
		if($winStreak > $bestWinStreak){
			++$bestWinStreak;
		}
		if($winStreakMode > $bestWinStreakMode){
			++$bestWinStreakMode;
		}
		$this->setDuelsBestWinStreak($bestWinStreak, $type, $mode, $bestWinStreakMode);
	}

	public function onDuelLoss(DuelType $type, DuelMode $mode) : void{
		$this->setDuelsLosses($this->getDuelsLosses($type) + 1, $type, $mode, $this->getDuelsLosses($type, $mode) + 1);
		$winStreak = $this->getDuelsWinStreak($type);
		if($winStreak > 0){
			$winStreakMode = $this->getDuelsWinStreak($type, $mode);
			$this->setDuelsWinStreak($winStreak - $winStreakMode, $type, $mode, 0);
		}
	}

	public function onDuelPlay(DuelType $type, DuelMode $mode) : void{
		$this->setDuelsPlays($this->getDuelsPlays($type) + 1, $type, $mode, $this->getDuelsPlays($type, $mode) + 1);
	}

	public function getBannedProfilesInQueue() : array{
		return $this->get('banned_profiles_in_queue', []);
	}

	public function addBannedProfileInQueue(Profile $banned) : void{
		$bannedPlayers = $this->getBannedProfilesInQueue();
		$bannedPlayers[] = $banned->getXuid();
		$this->set('banned_profiles_in_queue', $bannedPlayers);
	}

	public function isProfileBannedInQueue(Profile $profile) : bool{
		return in_array($profile->getXuid(), $this->getBannedProfilesInQueue());
	}

	public function saveKitLayout(KitLayout $kitLayout, Kit $kit) : void{
		$kitLayouts = $this->get('kit_layouts', []);
		$kitLayouts[$kit->value] = $kitLayout->getContents();
		$this->set('kit_layouts', $kitLayouts);
	}

	public function removeKitLayout(Kit $kit) : void{
		$kitLayouts = $this->get('kit_layouts', []);
		if(isset($kitLayouts[$kit->value])){
			unset($kitLayouts[$kit->value]);
		}
		$this->set('kit_layouts', $kitLayouts);
	}

	public function getKitLayout(Kit $kit) : ?KitLayout{
		$kitLayouts = $this->get('kit_layouts', []);

		if(!isset($kitLayouts[$kit->value])){
			return null;
		}

		$cleanKitLayout = array_map(function($data){
			return ['id' => $data['id']];
		}, $kitLayouts[$kit->value]);
		return isset($kitLayouts[$kit->value]) ? KitLayout::fromData($cleanKitLayout) : null;
	}

	public function setCurrencyAmount(Currency $currency, int|float $amount) : void{
		$currencies = $this->get('currencies', []);
		$currencies[$currency->getName()] = $amount;
		$this->set('currencies', $currencies);
	}

	public function getCurrencyAmount(Currency $currency) : int{
		return $this->get('currencies', [])[$currency->getName()] ?? $currency->getDefaultValue();
	}

	public function setClanId(?ObjectId $clanId) : void{
		$this->clanId = $clanId;
		$this->set('clanId', $clanId);
	}

	public function getClanId() : ?ObjectId{
		$this->clanId = $this->get('clanId');
		return $this->clanId;
	}

	public function setClanRole(?ClanRole $clanRole) : void{
		$this->clanRole = $clanRole;
		$this->set('clanRole', $clanRole?->value);
	}

	public function getClanRole() : ?ClanRole{
		$role = $this->get('clanRole');
		return $role !== null ? ClanRole::tryFrom($role) : null;
	}

	public function getClan() : ?Clan{
		return $this->clanId !== null ? Practice::getInstance()->getClanManager()->getClanById($this->clanId) : null;
	}

	public function getTotalMp(): int{
		if($this->totalMp === 0 && $this->get('total_mp') !== null){
			$this->totalMp = $this->get('total_mp', 0);
		}
		return $this->totalMp;
	}

	public function setTotalMp(int $totalMp): void{
		$this->totalMp = $totalMp;
		$this->set('total_mp', $this->totalMp);
	}

	public function addMp(int $amount): void{
		$this->totalMp += $amount;
		$this->set('total_mp', $this->totalMp);
	}

	public function subtractMp(int $amount): void{
		$this->totalMp = max(0, $this->totalMp - $amount);
		$this->set('total_mp', $this->totalMp);
	}

	public function addIssuedPunishment(string $type): void{
		$this->getIssuedPunishments();
		$this->issuedPunishments[] = ['type' => $type];
		$this->set('issued_punishments', $this->issuedPunishments);
	}

	public function addOnlineMinute(): void{
		$this->getOnlineMinutes();
		$this->onlineMinutes[] = time();
		$this->set('online_minutes', $this->onlineMinutes);
	}

	public function getIssuedPunishments(): array{
		if(empty($this->issuedPunishments) && $this->get('issued_punishments') !== null){
			$this->issuedPunishments = $this->get('issued_punishments', []);
		}
		return $this->issuedPunishments;
	}

	public function setIssuedPunishments(array $issuedPunishments): void{
		$this->issuedPunishments = $issuedPunishments;
		$this->set('issued_punishments', $this->issuedPunishments);
	}

	public function getOnlineMinutes(): array{
		if(empty($this->onlineMinutes) && $this->get('online_minutes') !== null){
			$this->onlineMinutes = $this->get('online_minutes', []);
		}
		return $this->onlineMinutes;
	}

	public function setOnlineMinutes(array $onlineMinutes): void{
		$this->onlineMinutes = $onlineMinutes;
		$this->set('online_minutes', $this->onlineMinutes);
	}

	public function set(string $key, mixed $value) : void{
		$this->values[$key] = $value;
	}

	public function get(string $key, mixed $default = null) : mixed{
		return $this->values[$key] ?? $default;
	}

	public function remove(string $key) : void{
		unset($this->values[$key]);
	}

	public function export() : array{
		return $this->values;
	}

	public function save() : void{
		Practice::getInstance()->getProfileManager()->saveProfile($this);
	}
}

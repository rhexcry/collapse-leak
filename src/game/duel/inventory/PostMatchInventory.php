<?php

declare(strict_types=1);

namespace collapse\game\duel\inventory;

use collapse\game\duel\item\DuelItems;
use collapse\game\duel\records\DuelRecord;
use collapse\game\statistics\GameStatistics;
use collapse\i18n\CollapseTranslationFactory;
use collapse\inventory\VirtualDoubleChestInventory;
use collapse\player\CollapsePlayer;
use collapse\player\rank\Rank;
use collapse\Practice;
use collapse\PracticeConstants;
use collapse\utils\TextUtils;
use pocketmine\block\utils\MobHeadType;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use pocketmine\lang\Translatable;
use pocketmine\utils\TextFormat;
use function array_map;
use function array_merge;
use function array_search;
use function date;
use function implode;
use function max;
use const EOL;

final class PostMatchInventory extends VirtualDoubleChestInventory{

	public function __construct(
		private readonly DuelRecord $record,
		private readonly string     $xuid,
		int                         $delay = 3
	){
		$profile = Practice::getPlayerByXuid($xuid)?->getProfile() ?? Practice::getInstance()->getProfileManager()->getProfileByXuid($xuid);
		parent::__construct(
			CollapseTranslationFactory::duels_post_match_inventory_name($profile->getRank()->toColor() . $profile->getPlayerName()),
			$delay
		);
		$this->setContents($record->getInventory($xuid));
	}

	private function addViewItem(CollapsePlayer $player, int $slot, string $xuid) : void{
		$profile = Practice::getPlayerByXuid($xuid)?->getProfile() ?? Practice::getInstance()->getProfileManager()->getProfileByXuid($xuid);
		$this->setItem($slot, DuelItems::VIEW_POST_MATCH_INVENTORY()
			->setPlayer($profile->getRank()->toColor() . $profile->getPlayerName())
			->setRecord($this->record)
			->setXuid($xuid)
			->translate($player)
		);
	}

	public function open(CollapsePlayer $player) : void{
		$translator = $player->getProfile()->getTranslator();
		$statistics = $this->record->getStatistics();

		if($player->getXuid() !== $this->xuid){
			if($player->getProfile()->getRank()->getPriority() >= Rank::ARCANE->getPriority() &&
				count($player->getProfile()->getBannedProfilesInQueue()) <= 3 &&
				!$player->getProfile()->isProfileBannedInQueue(Practice::getInstance()->getProfileManager()->getProfileByXuid($this->xuid))){

				$this->setItem(51, DuelItems::BLOCK_IN_QUEUE()
					->setCustomName($translator->translate(CollapseTranslationFactory::duels_item_block_in_queue()))
					->setLore([PracticeConstants::ITEM_LORE])
					->setXuid($this->xuid));
			}
		}



		foreach(
			[
				[47, VanillaBlocks::MOB_HEAD()->setMobHeadType(MobHeadType::SKELETON)->asItem()->setCustomName(
					$translator->translate(CollapseTranslationFactory::duels_post_match_inventory_item_health((string) ($statistics[GameStatistics::HEALTH][$this->xuid] ?? 0.0)))
				)->setLore([PracticeConstants::ITEM_LORE])],
				[48, VanillaItems::STEAK()->setCustomName(
					$translator->translate(CollapseTranslationFactory::duels_post_match_inventory_item_hunger((string) ($statistics[GameStatistics::HUNGER][$this->xuid] ?? 0.0)))
				)->setLore([PracticeConstants::ITEM_LORE])],
				[49, VanillaBlocks::BREWING_STAND()->asItem()->setCustomName(
					$translator->translate(CollapseTranslationFactory::duels_post_match_inventory_item_potion_effects(implode(EOL, array_map(static function(array $effect) use ($translator) : string{
						$amplifier = max($effect[1], 1);
						$duration = $effect[2] > 50_000 ? '∞' : date('i:s', $effect[2]);
						return TextFormat::AQUA . $translator->translate(new Translatable($effect[0])) . TextFormat::DARK_AQUA . ' ' . TextUtils::numberToRoman($amplifier) . ' ' . TextFormat::GRAY . $duration;
					}, $this->record->getPotionEffects($this->xuid)))))
				)->setLore([PracticeConstants::ITEM_LORE])],
				[50, VanillaItems::DIAMOND_SWORD()->setCustomName(
					$translator->translate(CollapseTranslationFactory::duels_post_match_inventory_item_combat(
						(string) ($statistics[GameStatistics::HITS][$this->xuid] ?? 0),
						(string) ($statistics[GameStatistics::CRITICAL_HITS][$this->xuid] ?? 0),
						(string) ($statistics[GameStatistics::MAX_COMBO][$this->xuid] ?? 0)
					))
				)->setLore([PracticeConstants::ITEM_LORE])]
			] as [$slot, $item]
		){
			$this->setItem($slot, $item);
		}

		$players = array_merge($this->record->getWinners(), $this->record->getLosers());
		$index = array_search($this->xuid, $players, true);

		if(isset($players[$index - 1])){
			$this->addViewItem($player, 45, $players[$index - 1]);
		}

		if(isset($players[$index + 1])){
			$this->addViewItem($player, 53, $players[$index + 1]);
		}

		parent::open($player);
	}
}

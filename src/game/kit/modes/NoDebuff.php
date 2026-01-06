<?php

declare(strict_types=1);

namespace collapse\game\kit\modes;

use collapse\game\kit\KitCollection;
use collapse\game\statistics\GameStatistics;
use collapse\game\statistics\GameStatisticsManager;
use collapse\i18n\CollapseTranslationFactory;
use collapse\item\default\CollapseEnderPearl;
use collapse\item\default\CollapseSplashPotion;
use collapse\player\CollapsePlayer;
use collapse\utils\InventoryUtils;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemTypeIds;
use pocketmine\item\PotionType;
use pocketmine\item\VanillaItems;
use pocketmine\utils\Limits;
use pocketmine\utils\TextFormat;

final class NoDebuff extends KitCollection{

	public function __construct(){
		$potions = [];
		$identifier = VanillaItems::SPLASH_POTION()->getTypeId();
		for($i = 0; $i < 34; ++$i){
			$potions[] = (new CollapseSplashPotion(new ItemIdentifier($identifier)))->setType(PotionType::STRONG_HEALING);
		}
		$identifier = VanillaItems::ENDER_PEARL()->getTypeId();
		parent::__construct(
			[
				VanillaItems::DIAMOND_HELMET()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION()))->setUnbreakable(),
				VanillaItems::DIAMOND_CHESTPLATE()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION()))->setUnbreakable(),
				VanillaItems::DIAMOND_LEGGINGS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION()))->setUnbreakable(),
				VanillaItems::DIAMOND_BOOTS()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::PROTECTION()))->setUnbreakable(),
			],
			[
				$this->markAsMainWeapon(VanillaItems::DIAMOND_SWORD()->addEnchantment(new EnchantmentInstance(VanillaEnchantments::UNBREAKING()))->setUnbreakable()),
				(new CollapseEnderPearl(new ItemIdentifier($identifier)))->setCount(16),
				...$potions
			],
			[
				new EffectInstance(VanillaEffects::SPEED(), Limits::INT32_MAX, visible: false)
			]
		);
	}

	public function addAdditionalStatistics(GameStatisticsManager $statisticsManager, array $players) : void{
		$statistics = new GameStatistics(GameStatistics::TOTAL_POTIONS, null);
		foreach($players as $player){
			$statistics->set($player, InventoryUtils::countItems($player->getInventory(), ItemTypeIds::SPLASH_POTION));
		}
		$statisticsManager->register($statistics);
		$statisticsManager->formatter(GameStatistics::THROW_POTIONS, static function(CollapsePlayer $target, CollapsePlayer $winner, CollapsePlayer $loser, GameStatistics $statistics) use ($statisticsManager) : string{
			return
				TextFormat::AQUA . $statistics->get($winner) . '/' . $statisticsManager->get(GameStatistics::TOTAL_POTIONS)->get($winner) . ' ' .
				TextFormat::WHITE . $statistics->translate($target) . ' ' .
				TextFormat::AQUA . $statistics->get($loser) . '/' . $statisticsManager->get(GameStatistics::TOTAL_POTIONS)->get($loser);
		});
		$statisticsManager->register(new GameStatistics(GameStatistics::THROW_POTIONS, CollapseTranslationFactory::game_statistics_throw_potions()));
	}
}

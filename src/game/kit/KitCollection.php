<?php

declare(strict_types=1);

namespace collapse\game\kit;

use collapse\game\statistics\GameStatisticsManager;
use collapse\item\TranslatableItem;
use collapse\player\CollapsePlayer;
use collapse\PracticeConstants;
use pocketmine\block\Wool;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\item\Armor;
use pocketmine\item\Item;
use pocketmine\item\ItemBlock;
use function array_map;

abstract class KitCollection{

	private Kit $type;

	public const string TAG_MAIN_WEAPON = 'mainWeapon';
	public const string TAG_TEAM_COLOR = 'teamColor';

	/**
	 * @param Armor[]          $armorContents
	 * @param Item[]           $contents
	 * @param EffectInstance[] $effects
	 */
	public function __construct(
		private array $armorContents,
		private array $contents,
		private array $effects
	){
		foreach($this->contents as $slot => $item){
			$tag = $item->getNamedTag()->setInt($item->getName() . $item->getTypeId(), $slot);
			$item->setNamedTag($tag);
		}
	}

	public function getArmorContents() : array{
		return $this->armorContents;
	}

	public function setContents(array $contents) : void{
		$this->contents = $contents;
	}

	public function getContents() : array{
		return $this->contents;
	}

	public function getEffects() : array{
		return $this->effects;
	}

	protected function markAsMainWeapon(Item $item) : Item{
		return $item->setNamedTag($item->getNamedTag()->setByte(self::TAG_MAIN_WEAPON, 1));
	}

	protected function markAsTeamColor(Item $item) : Item{
		return $item->setNamedTag($item->getNamedTag()->setByte(self::TAG_TEAM_COLOR, 1));
	}

	/**
	 * @param CollapsePlayer[] $players
	 */
	public function addAdditionalStatistics(GameStatisticsManager $statisticsManager, array $players) : void{}

	private static function setTeamColor(CollapsePlayer $player, Item $item) : Item{
		if($item->getNamedTag()->getByte(self::TAG_TEAM_COLOR, -1) === -1){
			return $item;
		}
		$team = $player->getTeam();
		if($team === null){
			return $item;
		}
		if($item instanceof Armor){
			$item->setCustomColor($team->getArmorColor());
		}elseif($item instanceof ItemBlock){
			$block = $item->getBlock();
			if($block instanceof Wool){
				$block->setColor($team->getDyeColor());
			}
			$item = $block
				->asItem()
				->setCount($item->getCount())
				->setNamedTag($item->getNamedTag())
				->setLore($item->getLore());
		}
		return $item;
	}

	public function applyTo(CollapsePlayer $player) : void{
		$player->getArmorInventory()->setContents(array_map(static function($armor) use ($player){
			if($armor instanceof TranslatableItem){
				$armor = $armor->translate($player);
			}
			return clone self::setTeamColor($player, $armor->setLore([PracticeConstants::ITEM_LORE]));
		}, $this->armorContents));
		$player->getInventory()->setContents(array_map(static function($content) use ($player){
			if($content instanceof TranslatableItem){
				$content = $content->translate($player);
			}
			return clone self::setTeamColor($player, $content->setLore([PracticeConstants::ITEM_LORE]));
		}, $this->contents));
		foreach($this->effects as $effect){
			$player->getEffects()->add(clone $effect);
		}
	}

	public function setType(Kit $type) : void{
		$this->type = $type;
	}

	public function getType() : Kit{
		return $this->type;
	}
}
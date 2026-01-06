<?php

declare(strict_types=1);

namespace collapse\system\shop;

use collapse\cosmetics\capes\Cape;
use collapse\cosmetics\effects\death\DeathEffectType;
use collapse\cosmetics\tags\ChatTag;
use collapse\i18n\CollapseTranslationFactory;
use collapse\i18n\TranslatorLocales;
use collapse\mongo\MongoWrapper;
use collapse\mongo\operation\DeleteManyOperation;
use collapse\mongo\operation\InsertOneOperation;
use collapse\mongo\operation\UpdateOneOperation;
use collapse\player\CollapsePlayer;
use collapse\player\profile\Profile;
use collapse\player\rank\Rank;
use collapse\Practice;
use collapse\resourcepack\CollapseUI;
use collapse\resourcepack\Font;
use collapse\system\shop\event\PlayerPurchaseEvent;
use collapse\system\shop\types\items\CapeItem;
use collapse\system\shop\types\items\ChatTagItem;
use collapse\system\shop\types\items\DeathEffectItem;
use collapse\system\shop\types\items\RankItem;
use collapse\system\shop\types\ProfileCollectibleShopItem;
use collapse\system\shop\types\ShopCategory;
use collapse\system\shop\types\ShopCategoryName;
use collapse\system\shop\types\ShopItem;
use collapse\utils\TextUtils;
use collapse\wallet\currency\Currencies;
use collapse\wallet\Wallet;
use MongoDB\Collection;
use MongoDB\Model\BSONDocument;
use pocketmine\lang\Translatable;
use function array_keys;
use function count;
use function time;

final class ShopManager{

	private const string PLAYER_PURCHASES_COLLECTION = 'player_purchases';
	private const string SHOP_ITEMS_COLLECTION = 'shop_items';

	private Collection $purchasesCollection;
	private Collection $shopItemsCollection;

	/** @var ShopCategory[] */
	private array $categories = [];

	/** @var ShopItem[] */
	private array $items = [];
	/** @var ShopItem[][] */
	private array $itemsByCategory = [];

	public function __construct(
		private readonly Practice $plugin
	){
		$this->purchasesCollection = MongoWrapper::getClient()->selectCollection(Practice::getDatabaseName(), self::PLAYER_PURCHASES_COLLECTION);
		$this->shopItemsCollection = MongoWrapper::getClient()->selectCollection(Practice::getDatabaseName(), self::SHOP_ITEMS_COLLECTION);

		$this->addDefaultCategories();
		$this->addDefaultItems();
		$this->syncShopItemsToDB();
	}

	public function addDefaultCategories() : void{
		$this->addCategory(new ShopCategory(
			ShopCategoryName::Ranks->value,
			CollapseTranslationFactory::shop_category_ranks_name(),
			CollapseTranslationFactory::shop_category_ranks_description(),
			CollapseUI::SHOP_RANKS_FORM_LOGO
		));

		$this->addCategory(new ShopCategory(
			ShopCategoryName::ChatTags->value,
			CollapseTranslationFactory::shop_category_chat_tags_name(),
			CollapseTranslationFactory::shop_category_chat_tags_description(),
			CollapseUI::COSMETICS_CHAT_TAGS_FORM_LOGO
		));

		$this->addCategory(new ShopCategory(
			ShopCategoryName::Capes->value,
			CollapseTranslationFactory::shop_category_capes_name(),
			CollapseTranslationFactory::shop_category_capes_description(),
			CollapseUI::COSMETICS_CAPE_FORM_LOGO
		));

		$this->addCategory(new ShopCategory(
			ShopCategoryName::DeathEffects->value,
			CollapseTranslationFactory::shop_category_death_effects_name(),
			CollapseTranslationFactory::shop_category_death_effects_description(),
			CollapseUI::COSMETICS_DEATH_EFFECT_FORM_LOGO
		));

		// до лучших времен
		/*$this->addCategory(new ShopCategory(
			ShopCategoryName::PotionColors->value,
			CollapseTranslationFactory::shop_category_potion_colors_name(),
			CollapseTranslationFactory::shop_category_potion_colors_description(),
			CollapseUI::COSMETICS_POTION_COLORS_FORM_LOGO
		));*/
	}

	public function addDefaultItems() : void{
		$this->addItem(new RankItem(
			Rank::YONKO,
			CollapseTranslationFactory::shop_item_ranks_luminous(Font::RANK_YONKO),
			CollapseTranslationFactory::shop_item_ranks_luminous(TextUtils::getNameWithFontedRank(Rank::YONKO, 'Dummy')),
			5_000,
			''
		));

		$this->addItem(new RankItem(
			Rank::NECESSARY,
			CollapseTranslationFactory::shop_item_ranks_luminous(Font::RANK_NECESSARY),
			CollapseTranslationFactory::shop_item_ranks_luminous(TextUtils::getNameWithFontedRank(Rank::NECESSARY, 'Dummy')),
			10_000,
			''
		));

		foreach(ChatTag::cases() as $chatTag){
			if($chatTag->getPrice() === null){
				continue;
			}
			$this->addItem(new ChatTagItem(
				$chatTag,
				CollapseTranslationFactory::shop_item_chat_tag_name($chatTag->toDisplayName()),
				CollapseTranslationFactory::shop_item_chat_tag_description(),
				$chatTag->getPrice(),
				''
			));
		}

		foreach(Cape::cases() as $cape){
			if($cape->getPrice() === null){
				continue;
			}
			$this->addItem(new CapeItem(
				$cape,
				CollapseTranslationFactory::shop_item_cape_name($cape->toDisplayName()),
				CollapseTranslationFactory::shop_item_cape_description($cape->toDisplayName()),
				$cape->getPrice(),
				''
			));
		}

		foreach(DeathEffectType::cases() as $deathEffect){
			if($deathEffect->getPrice() === null){
				continue;
			}
			$this->addItem(new DeathEffectItem(
				$deathEffect,
				CollapseTranslationFactory::shop_item_death_effect_name($deathEffect->toDisplayName()),
				CollapseTranslationFactory::shop_item_death_effect_description($deathEffect->toDisplayName()),
				$deathEffect->getPrice(),
				''
			));
		}
	}

	private function syncShopItemsToDB() : void{
		$defaultTranslator = $this->plugin->getTranslatorManager()->getDefaultTranslator();
		$russianTranslator = $this->plugin->getTranslatorManager()->fromLocale(TranslatorLocales::RUSSIAN);

		foreach($this->items as $item){
			$name = $item->getName() instanceof Translatable ? $defaultTranslator->translate($item->getName()) : $item->getName();

			$data = [
				'id' => $item->getId(),
				'categoryId' => $item->getCategoryId()->value,
				'name' => $name,
				'descriptionRu' => $russianTranslator->translate($item->getDescription()),
				'descriptionEn' => $defaultTranslator->translate($item->getDescription()),
				'price' => $item->getPrice(),
				'priceInRub' => $item->getPriceInRub(),
				'iconPath' => $item->getIconPath(),
				'onlyServer' => $item->isOnlyServer(),
				'commandToGive' => '',
				'websiteImage' => '',
			];

			if ($item instanceof RankItem) {
				$data['type'] = 'rank';
				$data['rank'] = $item->getRank()->value;
			} elseif ($item instanceof CapeItem) {
				$data['type'] = 'cape';
				$data['cape'] = $item->getCape()->value;
			} elseif ($item instanceof ChatTagItem) {
				$data['type'] = 'chat_tag';
				$data['chatTag'] = $item->getChatTag()->value;
			} elseif ($item instanceof DeathEffectItem) {
				$data['type'] = 'death_effect';
				$data['deathEffect'] = $item->getDeathEffect()->value;
			} else {
				$data['type'] = 'unknown';
			}

			MongoWrapper::push(new UpdateOneOperation(
				$this->shopItemsCollection->getDatabaseName(),
				$this->shopItemsCollection->getCollectionName(),
				['id' => $item->getId()],
				['$set' => $data],
				['upsert' => true]
			));
		}

		$codeItemIds = array_keys($this->items);
		MongoWrapper::push(new DeleteManyOperation(
			$this->shopItemsCollection->getDatabaseName(),
			$this->shopItemsCollection->getCollectionName(),
			['id' => ['$nin' => $codeItemIds]]
		));
	}

	public function addCategory(ShopCategory $category) : void{
		$this->categories[$category->getId()] = $category;
	}

	public function getCategory(ShopCategoryName $name) : ShopCategory{
		return $this->categories[$name->value] ?? throw new \RuntimeException('Category ' . $name->value . ' not found');
	}

	public function addItem(ShopItem $item) : void{
		if(isset($this->items[$item->getId()])){
			throw new ShopItemRegisteredException('Item ' . $item->getId() . ' already registered');
		}
		$this->items[$item->getId()] = $item;
		$this->itemsByCategory[$item->getCategoryId()->value][$item->getId()] = $item;
	}

	public function getItem(string $itemId) : ShopItem{
		return $this->items[$itemId] ?? throw new \RuntimeException('Item ' . $itemId . ' not found');
	}

	public function onPurchase(Profile $profile, ShopItem $item) : void{
		if($this->isItemPurchased($profile, $item)){
			if(($player = $profile->getPlayer()) instanceof CollapsePlayer){
				$player->sendTranslatedMessage(CollapseTranslationFactory::shop_item_already_purchased());
			}
			return;
		}

		if($item->getPrice() > Wallet::get(Currencies::DUST(), $profile)){
			if(($player = $profile->getPlayer()) instanceof CollapsePlayer){
				$player->sendTranslatedMessage(CollapseTranslationFactory::shop_insufficient_dust());
			}
			return;
		}

		if(!$item->onTryPurchase($profile)){
			$item->onPurchaseFailed($profile);
			return;
		}

		$item->onPurchaseSuccess($profile);

		Wallet::reduce(Currencies::DUST(), $profile, $item->getPrice());

		MongoWrapper::push(new InsertOneOperation(
			$this->purchasesCollection->getDatabaseName(),
			$this->purchasesCollection->getCollectionName(),
			[
				'playerXuid' => $profile->getXuid(),
				'itemId' => $item->getId(),
				'categoryId' => $item->getCategoryId(),
				'purchasedAt' => time()
			]
		));

		(new PlayerPurchaseEvent($profile, $item))->call();

		if(($player = $profile->getPlayer()) instanceof CollapsePlayer){
			$player->sendTranslatedMessage(CollapseTranslationFactory::shop_purchase_success($item->getName()));
		}

		$profile->save();
	}

	public function isItemPurchased(Profile $profile, ShopItem $item) : bool{
		if($item instanceof ProfileCollectibleShopItem){
			return $item->isCollected($profile);
		}

		$result = $this->purchasesCollection->findOne([
			'itemId' => $item->getId(),
			'playerXuid' => $profile->getXuid()
		]);

		return $result instanceof BSONDocument;
	}

	public function getAllCategories() : array{
		return $this->categories;
	}

	public function getPurchasedItemsCountByCategory(Profile $profile, ShopCategoryName $categoryName) : int{
		$itemIds = array_keys($this->itemsByCategory[$categoryName->value] ?? []);

		if(empty($itemIds)){
			return 0;
		}

		return $this->purchasesCollection->countDocuments([
			'playerXuid' => $profile->getXuid(),
			'itemId' => ['$in' => $itemIds]
		]);
	}

	public function getTotalItemsCountInCategory(ShopCategoryName $categoryName) : int{
		return count($this->itemsByCategory[$categoryName->value] ?? []);
	}

	/**
	 * @return ShopItem[]
	 */
	public function getItemsByCategory(ShopCategoryName $categoryName) : array{
		return $this->itemsByCategory[$categoryName->value] ?? [];
	}
}

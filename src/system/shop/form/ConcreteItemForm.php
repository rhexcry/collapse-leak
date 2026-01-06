<?php

declare(strict_types=1);

namespace collapse\system\shop\form;

use collapse\form\SimpleForm;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\resourcepack\Font;
use collapse\system\shop\types\items\RankItem;
use collapse\system\shop\types\ShopCategoryName;
use collapse\system\shop\types\ShopItem;
use collapse\wallet\currency\Currencies;
use collapse\wallet\Wallet;

final class ConcreteItemForm extends SimpleForm{

	private bool $canBuy = true;

	public function __construct(CollapsePlayer $player, ShopItem $item){
		$shopManager = Practice::getInstance()->getShopManager();
		parent::__construct(function(CollapsePlayer $player, ?int $data = null) use ($shopManager, $item) : void{
			if($data === null){
				return;
			}

			if(!$this->canBuy){
				$player->sendForm(new ShopMenuForm($player));
				return;
			}

			$shopManager->onPurchase($player->getProfile(), $item);
		});

		$profile = $player->getProfile();
		$translator = $profile->getTranslator();

		if($item->getCategoryId() === ShopCategoryName::ChatTags || $item->getCategoryId() === ShopCategoryName::PotionColors){
			$this->setTitle($translator->translate($item->getName()));
		}else{
			$this->setTitle(Font::bold($translator->translate($item->getName())));
		}

		if($shopManager->isItemPurchased($profile, $item)){
			$this->setContent(Font::text($translator->translate(CollapseTranslationFactory::shop_item_already_purchased())));
			$this->canBuy = false;
			$this->addButton(Font::bold($translator->translate(CollapseTranslationFactory::form_button_go_back())));
			return;
		}

		if($item instanceof RankItem && $profile->getRank()->getPriority() >= $item->getRank()->getPriority()){
			$this->setContent(Font::text($translator->translate(CollapseTranslationFactory::shop_item_ranks_have_rank_already())));
			$this->canBuy = false;
			$this->addButton(Font::bold($translator->translate(CollapseTranslationFactory::form_button_go_back())));
			return;
		}

		if(Wallet::get(Currencies::DUST(), $profile) < $item->getPrice()){
			$this->setContent(Font::text($translator->translate(CollapseTranslationFactory::shop_insufficient_dust())));
			$this->canBuy = false;
			$this->addButton(Font::bold($translator->translate(CollapseTranslationFactory::form_button_go_back())));
			return;
		}

		$this->setContent(Font::text($translator->translate($item->getDescription())));
		$this->addButton(Font::bold($translator->translate(CollapseTranslationFactory::shop_form_button_buy())));
	}
}

<?php

declare(strict_types=1);

namespace collapse\system\shop\form;

use collapse\form\SimpleForm;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\resourcepack\Font;
use collapse\system\shop\types\ShopCategory;
use collapse\system\shop\types\ShopCategoryName;
use function array_values;

final class OpenCategoryForm extends SimpleForm{

	public function __construct(CollapsePlayer $player, ShopCategory $category){
		$shopManager = Practice::getInstance()->getShopManager();
		$items = $shopManager->getItemsByCategory(ShopCategoryName::from($category->getId()));
		parent::__construct(static function(CollapsePlayer $player, ?int $data = null) use ($shopManager, $items) : void{
			if($data === null){
				return;
			}
			$player->sendForm(new ConcreteItemForm($player, array_values($items)[$data]));
		});

		$translator = $player->getProfile()->getTranslator();

		$this->setTitle(Font::bold($translator->translate($category->getName())));

		$this->setContent(Font::text($translator->translate(CollapseTranslationFactory::shop_form_open_category_text())));
		foreach($items as $item){
			$price = $translator->translate(CollapseTranslationFactory::shop_form_item_price((string) $item->getPrice()));
			if($item->getCategoryId() === ShopCategoryName::ChatTags || $item->getCategoryId() === ShopCategoryName::PotionColors){
				$this->addButton($translator->translate($item->getName()) . $price);
			}else{
				$this->addButton(Font::bold($translator->translate($item->getName())) . $price);
			}
		}
	}
}

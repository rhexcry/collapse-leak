<?php

declare(strict_types=1);

namespace collapse\system\shop\form;

use collapse\form\SimpleForm;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\resourcepack\CollapseUI;
use collapse\resourcepack\Font;
use collapse\system\exchange\ExchangeForm;
use collapse\system\shop\types\ShopCategoryName;
use function array_values;
use const EOL;

final class ShopMenuForm extends SimpleForm{

	public function __construct(CollapsePlayer $player){
		$shopManager = Practice::getInstance()->getShopManager();
		$categories = $shopManager->getAllCategories();
		parent::__construct(static function(CollapsePlayer $player, ?int $data = null) use ($categories) : void{
			if($data === null){
				return;
			}
			$categories = array_values($categories);
			if(isset($categories[$data])){
				$player->sendForm(new OpenCategoryForm($player, $categories[$data]));
			}else{
				$player->sendForm(new ExchangeForm($player));
			}
		});

		$profile = $player->getProfile();
		$translator = $profile->getTranslator();

		$this->setTitle(CollapseUI::HEADER_FORM_GRID . Font::bold($translator->translate(CollapseTranslationFactory::shop_form_title())));
		$this->setContent(Font::text($translator->translate(CollapseTranslationFactory::shop_form_text())));

		foreach($categories as $category){
			if($category->getId() === ShopCategoryName::Ranks->value){
				$this->addButton(
					Font::bold($translator->translate(CollapseTranslationFactory::shop_form_button_category_without_stats($category->getName()))) . EOL . EOL .
					Font::bold($translator->translate(CollapseTranslationFactory::shop_category_ranks_description())),
					self::IMAGE_TYPE_PATH,
					$category->getIconPath()
				);
				continue;
			}
			$this->addButton(
				Font::whiteBold($translator->translate(CollapseTranslationFactory::shop_form_button_category(
					Font::bold($translator->translate($category->getName())),
					(string) $shopManager->getPurchasedItemsCountByCategory($profile, ShopCategoryName::from($category->getId())),
					(string) $shopManager->getTotalItemsCountInCategory(ShopCategoryName::from($category->getId())),
				))) . EOL . EOL . Font::bold($translator->translate($category->getDescription())),
				self::IMAGE_TYPE_PATH, $category->getIconPath()
			);
		}

		$this->addButton(
			Font::bold($translator->translate(CollapseTranslationFactory::shop_button_exchange())),
			self::IMAGE_TYPE_PATH,
			CollapseUI::DUST_EXCHANGE_FORM_LOGO
		);
	}
}

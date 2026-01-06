<?php

declare(strict_types=1);

namespace collapse\system\exchange;

use collapse\form\SimpleForm;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\resourcepack\Font;
use collapse\wallet\currency\ExchangeResult;
use collapse\wallet\currency\StarCurrency;
use collapse\world\sound\MinecraftSound;
use collapse\world\sound\MinecraftSoundNames;
use function number_format;

final class ExchangeForm extends SimpleForm{

	private const int BUTTON_EXCHANGE_ALL = 0;
	private const int BUTTON_EXCHANGE_CUSTOM_AMOUNT = 1;

	public function __construct(CollapsePlayer $player){
		parent::__construct(static function(CollapsePlayer $player, ?int $data = null) : void{
			if($data === null){
				return;
			}
			switch($data){
				case self::BUTTON_EXCHANGE_ALL:
					if(StarCurrency::exchangeFromDust($player->getProfile(), $result, $source) === ExchangeResult::Success){
						$player->sendTranslatedMessage(CollapseTranslationFactory::exchange_form_successfully(
							number_format($source),
							number_format($result)
						));
						$player->getWorld()->addSound($player->getLocation(), new MinecraftSound(MinecraftSoundNames::RANDOM_LEVELUP), [$player]);
					}else{
						$player->sendTranslatedMessage(CollapseTranslationFactory::exchange_form_not_enough_dust());
					}
					break;
				case self::BUTTON_EXCHANGE_CUSTOM_AMOUNT:
					$player->sendForm(new ExchangeAmountForm($player));
					break;
			}
		});
		$translator = $player->getProfile()->getTranslator();
		$this->setTitle(Font::bold($translator->translate(CollapseTranslationFactory::exchange_form_title())));
		$this->setContent(Font::text($translator->translate(CollapseTranslationFactory::exchange_form_content())));
		$this->addButton(Font::bold($translator->translate(CollapseTranslationFactory::exchange_form_button_all())));
		$this->addButton(Font::bold($translator->translate(CollapseTranslationFactory::exchange_form_button_amount())));
	}
}

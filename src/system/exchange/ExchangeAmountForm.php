<?php

declare(strict_types=1);

namespace collapse\system\exchange;

use collapse\form\CustomForm;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\resourcepack\Font;
use collapse\wallet\currency\ExchangeResult;
use collapse\wallet\currency\StarCurrency;
use collapse\world\sound\MinecraftSound;
use collapse\world\sound\MinecraftSoundNames;
use function is_int;
use function is_numeric;
use function number_format;

final class ExchangeAmountForm extends CustomForm{

	public function __construct(CollapsePlayer $player){
		parent::__construct(static function(CollapsePlayer $player, ?array $data = null) : void{
			if($data === null){
				return;
			}
			$amount = $data[0] ?? null;
			if(!is_numeric($amount) || !is_int((int) $amount) || ((int) $amount) < 0){
				$player->sendTranslatedMessage(CollapseTranslationFactory::exchange_amount_form_incorrect_input());
				return;
			}
			if(StarCurrency::exchangeFromDust($player->getProfile(), $result, $source, (int) $amount) === ExchangeResult::NotEnoughSource){
				$player->sendTranslatedMessage(CollapseTranslationFactory::exchange_form_not_enough_dust());
				return;
			}
			$player->sendTranslatedMessage(CollapseTranslationFactory::exchange_form_successfully(
				number_format($source),
				number_format($result)
			));
			$player->getWorld()->addSound($player->getLocation(), new MinecraftSound(MinecraftSoundNames::RANDOM_LEVELUP), [$player]);
		});
		$this->setTitle(Font::bold($player->getProfile()->getTranslator()->translate(CollapseTranslationFactory::exchange_form_title())));
		$this->addInput(CollapseTranslationFactory::exchange_amount_form_input());
	}
}

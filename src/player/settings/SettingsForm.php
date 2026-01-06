<?php

declare(strict_types=1);

namespace collapse\player\settings;

use collapse\form\CustomForm;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\resourcepack\Font;
use function array_values;

final class SettingsForm extends CustomForm{

	public function __construct(CollapsePlayer $player){
		$profile = $player->getProfile();
		parent::__construct(static function(CollapsePlayer $player, mixed $data) use ($profile) : void{
			if($data === null){
				return;
			}
			$settings = array_values(Setting::cases());
			$changed = false;
			foreach($data as $index => $value){
				if($profile->getSetting($settings[$index]) !== $value){
					$changed = true;
					$profile->setSetting($settings[$index], $value);
				}
			}
			if($changed){
				$profile->save();
				$player->sendTranslatedMessage(CollapseTranslationFactory::settings_saved());
			}
		});
		$translator = $player->getProfile()->getTranslator();
		foreach(Setting::cases() as $setting){
			$this->addToggle(Font::text($translator->translate($setting->toName())), $profile->getSetting($setting));
		}
		$this->setTitle(Font::bold($translator->translate(CollapseTranslationFactory::settings_form_title())));
	}
}

<?php

declare(strict_types=1);

namespace collapse\system\kiteditor\form;

use collapse\form\SimpleForm;
use collapse\game\kit\Kit;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\resourcepack\Font;
use function array_values;

final class KitSelectionForm extends SimpleForm{

	public function __construct(CollapsePlayer $player){
		$kits = array_filter(Kit::cases(), function(Kit $kit) : bool{
			return $kit !== Kit::SkyWars && $kit !== Kit::Sumo;
		});
		parent::__construct(static function(CollapsePlayer $player, ?int $data = null) use ($kits) : void{
			if($data === null){
				return;
			}

			$allKits = array_values($kits);

			if(!isset($allKits[$data])){
				return;
			}

			Practice::getInstance()->getKitEditorManager()->startEditing($player, $allKits[$data]);
		});

		$translator = $player->getProfile()->getTranslator();
		$this->setTitle(Font::bold($translator->translate(CollapseTranslationFactory::kit_editor_form_kit_editor_title())));
		foreach($kits as $kit){
			$this->addButton(Font::bold($kit->toDisplayName()));
		}
	}
}
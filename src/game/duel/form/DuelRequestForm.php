<?php

declare(strict_types=1);

namespace collapse\game\duel\form;

use collapse\form\SimpleForm;
use collapse\game\duel\types\DuelMode;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\resourcepack\CollapseUI;
use collapse\resourcepack\Font;
use Symfony\Component\Filesystem\Path;

final class DuelRequestForm extends SimpleForm{

	public function __construct(CollapsePlayer $player, CollapsePlayer $target){
		$modes = DuelMode::cases();
		parent::__construct(static function(CollapsePlayer $player, ?int $data = null) use ($target, $modes) : void{
			if($data === null){
				return;
			}

			if($target === null || !$target->isConnected()){
				$player->sendTranslatedMessage(CollapseTranslationFactory::player_not_found());
				return;
			}

			if(isset($modes[$data])){
				Practice::getInstance()->getDuelManager()->getRequestManager()->send($target, $player, $modes[$data]);
			}
		});
		$this->setTitle(CollapseTranslationFactory::duels_form_request_title($target->getNameWithRankColor()));
		foreach($modes as $mode){
			$this->addButton(
				Font::bold($mode->toDisplayName()),
				SimpleForm::IMAGE_TYPE_PATH,
				Path::join(CollapseUI::GAME_MODE_ICONS, $mode->toTexture())
			);
		}
	}
}

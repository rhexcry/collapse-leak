<?php

declare(strict_types=1);

namespace collapse\game\duel\form;

use collapse\form\SimpleForm;
use collapse\game\duel\types\DuelMode;
use collapse\game\duel\types\DuelType;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\resourcepack\CollapseUI;
use collapse\resourcepack\Font;
use Symfony\Component\Filesystem\Path;

final class UnrankedDuelsForm extends SimpleForm{

	public function __construct(CollapsePlayer $player){
		$modes = DuelMode::cases();
		$duelManager = Practice::getInstance()->getDuelManager();
		$queueManager = $duelManager->getQueueManager();
		parent::__construct(static function(CollapsePlayer $player, ?int $data = null) use ($modes, $queueManager) : void{
			if($data === null){
				return;
			}
			if(isset($modes[$data])){
				$queueManager->joinSoloQueue($player, $modes[$data]);
			}
		});
		$translator = $player->getProfile()->getTranslator();
		$this->setTitle(CollapseUI::HEADER_FORM_GRID . Font::bold($translator->translate(CollapseTranslationFactory::unranked_duels_form_title())));
		foreach($modes as $mode){
			$this->addButton(
				$translator->translate(CollapseTranslationFactory::duels_form_button_unranked_mode(
					Font::bold($mode->toDisplayName()),
					(string) $queueManager->getSoloUnrankedInQueue($mode),
					(string) $duelManager->getPlaying(DuelType::Unranked, $mode)
				)),
				SimpleForm::IMAGE_TYPE_PATH,
				Path::join(CollapseUI::GAME_MODE_ICONS, $mode->toTexture())
			);
		}
	}
}

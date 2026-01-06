<?php

declare(strict_types=1);

namespace collapse\game\duel\form;

use collapse\form\SimpleForm;
use collapse\game\duel\Duel;
use collapse\game\teams\Team;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\resourcepack\CollapseUI;
use collapse\resourcepack\Font;
use Symfony\Component\Filesystem\Path;
use function array_rand;
use function array_values;

final class SpectateDuelsForm extends SimpleForm{

	public function __construct(){
		$duels = Practice::getInstance()->getDuelManager()->getDuels();
		parent::__construct(static function(CollapsePlayer $player, ?int $data = null) use ($duels) : void{
			if($data === null){
				return;
			}
			$lobbyManager = Practice::getInstance()->getLobbyManager();
			if(!$lobbyManager->isInLobby($player)){
				$player->sendTranslatedMessage(CollapseTranslationFactory::command_spectate_not_in_lobby());
				return;
			}
			/** @var Duel[] $duels */
			$duels = array_values($duels);
			if(!isset($duels[$data])){
				$player->sendForm(new DuelsForm($player));
				return;
			}
			$playerManager = $duels[$data]->getPlayerManager();
			if(empty($playerManager->getPlayers())){
				return;
			}
			$randomPlayer = $playerManager->getPlayers()[array_rand($playerManager->getPlayers())] ?? null;
			$duels[$data]->getSpectatorManager()->addSpectator($player, $randomPlayer);
		});
		$this->setTitle(CollapseTranslationFactory::duels_spectate_form_title());
		if(empty($duels)){
			$this->setContent(CollapseTranslationFactory::duels_spectate_form_no_duels());
		}else{
			foreach($duels as $duel){
				/** @var (CollapsePlayer|Team)[] $opponents */
				$opponents = array_values($duel->getOpponentManager()->getOpponents());
				$this->addButton(CollapseTranslationFactory::duels_spectate_form_button(
					$opponents[0] instanceof CollapsePlayer ? $opponents[0]->getNameWithRankColor() : $opponents[0]->getName(),
					$opponents[1] instanceof CollapsePlayer ? $opponents[1]->getNameWithRankColor() : $opponents[1]->getName(),
					Font::bold($duel->getConfig()->getMode()->toDisplayName())
				), SimpleForm::IMAGE_TYPE_PATH, Path::join(CollapseUI::GAME_MODE_ICONS, $duel->getConfig()->getMode()->toTexture()));
			}
		}
		$this->addButton(CollapseTranslationFactory::form_button_go_back());
	}
}

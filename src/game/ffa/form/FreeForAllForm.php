<?php

declare(strict_types=1);

namespace collapse\game\ffa\form;

use collapse\form\SimpleForm;
use collapse\game\ffa\FreeForAllArena;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\resourcepack\CollapseUI;
use collapse\resourcepack\Font;
use Symfony\Component\Filesystem\Path;
use function array_values;
use function count;

final class FreeForAllForm extends SimpleForm{

	public function __construct(CollapsePlayer $player){
		$arenas = Practice::getInstance()->getFreeForAllManager()->getArenas();
		parent::__construct(static function(CollapsePlayer $player, ?int $data = null) use ($arenas) : void{
			if($data === null){
				return;
			}
			if($player->getGame() !== null){
				return;
			}

			/** @var FreeForAllArena[] $arenas */
			$arenas = array_values($arenas);
			if(isset($arenas[$data])){
				$arenas[$data]->onPlayerJoin($player, function(FreeForAllArena $game) use ($player) : void{
					Practice::getInstance()->getLobbyManager()->removeFromLobby($player);
					$game->getPlayerManager()->addPlayer($player);
				});
			}
		});
		$translator = $player->getProfile()->getTranslator();
		foreach($arenas as $arena){
			$this->addButton(
				$translator->translate(CollapseTranslationFactory::free_for_all_form_button(
					Font::bold($arena->getConfig()->getMode()->toDisplayName()),
					(string) count($arena->getPlayerManager()->getPlayers())
				)),
				SimpleForm::IMAGE_TYPE_PATH,
				Path::join(CollapseUI::GAME_MODE_ICONS, $arena->getConfig()->getMode()->toTexture())
			);
		}
		$this->setTitle(CollapseUI::HEADER_FORM_GRID . Font::bold('Free For All'));
	}
}

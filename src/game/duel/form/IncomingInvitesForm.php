<?php

declare(strict_types=1);

namespace collapse\game\duel\form;

use collapse\form\SimpleForm;
use collapse\game\duel\requests\DuelRequest;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\resourcepack\CollapseUI;
use collapse\resourcepack\Font;
use Symfony\Component\Filesystem\Path;
use function array_filter;
use function array_values;
use const EOL;

final class IncomingInvitesForm extends SimpleForm{

	public function __construct(CollapsePlayer $player){
		$requestManager = Practice::getInstance()->getDuelManager()->getRequestManager();
		/** @var DuelRequest[] $requests */
		$requests = array_filter(array_values($requestManager->getRequests($player)), static function(DuelRequest $request) : bool{
			return Practice::getPlayerByXuid($request->getSenderXuid()) !== null;
		});
		parent::__construct(static function(CollapsePlayer $player, ?int $data = null) use ($requestManager, $requests) : void{
			if($data === null){
				return;
			}
			if(isset($requests[$data])){
				$requestManager->accept($requests[$data]);
			}
		});
		$this->setTitle(CollapseTranslationFactory::duels_incoming_invites_form_title());
		foreach($requests as $request){
			$sender = Practice::getPlayerByXuid($request->getSenderXuid());
			$this->addButton(
				Font::bold($request->getMode()->toDisplayName()) . EOL .
				$sender->getNameWithRankColor(),
				SimpleForm::IMAGE_TYPE_PATH,
				Path::join(CollapseUI::GAME_MODE_ICONS, $request->getMode()->toTexture())
			);
		}
	}
}

<?php

declare(strict_types=1);

namespace collapse\system\friend\form;

use collapse\form\SimpleForm;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\resourcepack\Font;
use collapse\system\friend\request\FriendRequest;

final class OutgoingRequestConcreteForm extends SimpleForm{

	public function __construct(CollapsePlayer $player, FriendRequest $request){
		parent::__construct(function(CollapsePlayer $player, ?int $data = null) use ($request) : void{
			if($data === null){
				return;
			}

			$friendManager = Practice::getInstance()->getFriendManager();

			match($data){
				0 => $friendManager->cancelFriendRequest($player->getProfile(), $request),
			};
		});

		$translator = $player->getProfile()->getTranslator();

		$this->setTitle(Font::bold(Practice::getInstance()->getProfileManager()->getProfileByXuid($request->getReceiverXuid())->getPlayerName()));
		$this->addButton(Font::bold($translator->translate(CollapseTranslationFactory::friend_form_outgoing_request_cancel())));
		$this->addButton(Font::bold($translator->translate(CollapseTranslationFactory::form_button_go_back())));
	}
}

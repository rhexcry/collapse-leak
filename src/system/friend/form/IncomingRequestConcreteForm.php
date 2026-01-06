<?php

declare(strict_types=1);

namespace collapse\system\friend\form;

use collapse\form\SimpleForm;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\resourcepack\Font;
use collapse\system\friend\request\FriendRequestStatus;

final class IncomingRequestConcreteForm extends SimpleForm{

	public function __construct(CollapsePlayer $player, string $senderName){
		parent::__construct(static function(CollapsePlayer $player, ?int $data = null) use ($senderName) : void{
			if($data === null){
				return;
			}

			$friendManager = Practice::getInstance()->getFriendManager();
			$friendRequest = $friendManager->getFriendRequest(Practice::getInstance()->getProfileManager()->getProfileByName($senderName), $player->getProfile(), FriendRequestStatus::Pending);

			match($data){
				0 => $friendManager->acceptFriendRequest($player, $friendRequest),
				1 => $friendManager->rejectFriendRequest($player->getProfile(), $friendRequest)
			};
		});

		$translator = $player->getProfile()->getTranslator();

		$this->setTitle(Font::bold($senderName));
		$this->addButton(Font::bold($translator->translate(CollapseTranslationFactory::friend_form_incoming_request_concrete_accept())));
		$this->addButton(Font::bold($translator->translate(CollapseTranslationFactory::friend_form_incoming_request_concrete_decline())));
	}
}

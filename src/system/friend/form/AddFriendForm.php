<?php

declare(strict_types=1);

namespace collapse\system\friend\form;

use collapse\form\CustomForm;

use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\resourcepack\Font;

final class AddFriendForm extends CustomForm{

	public function __construct(CollapsePlayer $player){
		parent::__construct(static function(CollapsePlayer $player, mixed $data) : void{
			if($data === null){
				return;
			}

			$profile = Practice::getInstance()->getProfileManager()->getProfileByName($data[0]);

			if($profile === null){
				$player->sendTranslatedMessage(CollapseTranslationFactory::player_not_found());
				return;
			}

			if($profile->getLowerCasePlayerName() === $player->getProfile()->getLowerCasePlayerName()){
				$player->sendTranslatedMessage(CollapseTranslationFactory::friend_cant_request_itself());
				return;
			}

			Practice::getInstance()->getFriendManager()->sendFriendRequest($player, $profile);
		});

		$translator = $player->getProfile()->getTranslator();

		$this->setTitle(Font::bold($player->getProfile()->getTranslator()->translate(CollapseTranslationFactory::friend_form_friends_add())));
		$this->addInput(
			Font::bold($translator->translate(CollapseTranslationFactory::friend_form_add_friend_playerName_label())),
			Font::bold($translator->translate(CollapseTranslationFactory::friend_form_add_friend_playerName_input()))
		);
	}
}

<?php

declare(strict_types=1);

namespace collapse\system\friend\form;

use collapse\form\SimpleForm;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\resourcepack\Font;
use collapse\system\friend\Friend;

final class MyFriendsConcreteForm extends SimpleForm{

	public function __construct(CollapsePlayer $player, Friend $friend){
		parent::__construct(function(CollapsePlayer $player, ?int $data = null) use ($friend) : void{
			if($data === null){
				return;
			}

			$friendManager = Practice::getInstance()->getFriendManager();

			match($data){
				0 => $friendManager->removeFriend($player->getProfile(), Practice::getInstance()->getProfileManager()->getProfileByXuid($friend->getXuid())),
				1 => $player->sendForm(new MyFriendsForm($player))
			};
		});

		$translator = $player->getProfile()->getTranslator();

		$this->setTitle(Font::bold(Practice::getInstance()->getProfileManager()->getProfileByXuid($friend->getXuid())->getPlayerName()));
		$this->addButton(Font::bold($translator->translate(CollapseTranslationFactory::friend_form_my_friends_concrete_delete())));
		$this->addButton(Font::bold($translator->translate(CollapseTranslationFactory::form_button_go_back())));
	}
}

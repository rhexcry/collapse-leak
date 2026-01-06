<?php

declare(strict_types=1);

namespace collapse\system\friend\form;

use collapse\form\SimpleForm;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\resourcepack\Font;
use pocketmine\utils\TextFormat;
use function count;

final class MyFriendsForm extends SimpleForm{

	public function __construct(CollapsePlayer $player){
		$friends = Practice::getInstance()
			->getFriendManager()
			->getFriends($player->getProfile());
		parent::__construct(function(CollapsePlayer $player, ?int $data = null) use ($friends) : void{
			if($data === null){
				return;
			}

			$friend = $friends[$data] ?? null;
			if($friend === null){
				$player->sendForm(new FriendsForm($player));
				return;
			}

			$player->sendForm(new MyFriendsConcreteForm($player, $friend));
		});

		$translator = $player->getProfile()->getTranslator();
		$this->setTitle(Font::bold($translator->translate(CollapseTranslationFactory::friend_form_friends_my_friends())));

		if(count($friends) === 0){
			$this->setContent(Font::bold($translator->translate(CollapseTranslationFactory::friend_form_friends_no_friends())));
			$this->addButton(Font::bold($translator->translate(CollapseTranslationFactory::form_button_go_back())));
			return;
		}

		foreach($friends as $friend){
			$friendName = Practice::getInstance()
				->getProfileManager()
				->getProfileByXuid($friend->getXuid())
				->getPlayerName();

			$this->addButton(TextFormat::WHITE . $friendName);
		}

		$this->addButton(Font::bold($translator->translate(CollapseTranslationFactory::form_button_go_back())));
	}
}

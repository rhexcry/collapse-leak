<?php

declare(strict_types=1);

namespace collapse\system\friend\form;

use collapse\form\SimpleForm;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\resourcepack\CollapseUI;
use collapse\resourcepack\Font;

final class FriendsForm extends SimpleForm{

	private const int BUTTON_MY_FRIENDS = 0;
	private const int BUTTON_ADD_FRIEND = 1;
	private const int BUTTON_INCOMING_REQUESTS = 2;
	private const int BUTTON_OUTGOING_REQUESTS = 3;

	public function __construct(CollapsePlayer $player){
		parent::__construct(static function(CollapsePlayer $player, ?int $data = null) : void{
			if($data === null){
				return;
			}

			$player->sendForm(match($data){
				self::BUTTON_MY_FRIENDS => new MyFriendsForm($player),
				self::BUTTON_ADD_FRIEND => new AddFriendForm($player),
				self::BUTTON_INCOMING_REQUESTS => new IncomingRequestsForm($player),
				self::BUTTON_OUTGOING_REQUESTS => new OutgoingRequestsForm($player),
			});
		});

		$translator = $player->getProfile()->getTranslator();
		$this->setTitle(Font::bold($translator->translate(CollapseTranslationFactory::friend_form_friends_title())));
		$this->addButton(
			Font::bold($translator->translate(CollapseTranslationFactory::friend_form_friends_my_friends())),
			SimpleForm::IMAGE_TYPE_PATH,
			CollapseUI::FRIENDS_FORM_LOGO
		);

		$this->addButton(
			Font::bold($translator->translate(CollapseTranslationFactory::friend_form_friends_add())),
			SimpleForm::IMAGE_TYPE_PATH,
			CollapseUI::FRIEND_ADD_FORM_LOGO
		);

		$this->addButton(
			Font::bold($translator->translate(CollapseTranslationFactory::friend_form_friends_incoming())),
			SimpleForm::IMAGE_TYPE_PATH,
			CollapseUI::FRIENDS_INCOMING_FORM_LOGO
		);

		$this->addButton(
			Font::bold($translator->translate(CollapseTranslationFactory::friend_form_friends_outgoing())),
			SimpleForm::IMAGE_TYPE_PATH,
			CollapseUI::FRIENDS_OUTGOING_FORM_LOGO
		);
	}
}

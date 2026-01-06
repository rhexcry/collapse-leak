<?php

declare(strict_types=1);

namespace collapse\system\friend\form;

use collapse\form\SimpleForm;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\resourcepack\Font;
use pocketmine\utils\TextFormat;

final class OutgoingRequestsForm extends SimpleForm{

	public function __construct(CollapsePlayer $player){
		$requests = Practice::getInstance()->getFriendManager()->getOutgoingRequests($player->getProfile());
		parent::__construct(function(CollapsePlayer $player, ?int $data = null) use ($requests) : void{
			if($data === null){
				return;
			}
			$request = $requests[$data] ?? null;
			if($request === null){
				$player->sendForm(new FriendsForm($player));
				return;
			}
			$player->sendForm(new OutgoingRequestConcreteForm($player, $request));
		});

		$translator = $player->getProfile()->getTranslator();
		$this->setTitle(Font::bold($translator->translate(CollapseTranslationFactory::friend_form_friends_outgoing())));

		if(empty($requests)){
			$this->setContent(Font::bold($translator->translate(CollapseTranslationFactory::friend_form_friends_incoming_no_requests())));
			$this->addButton(Font::bold($translator->translate(CollapseTranslationFactory::form_button_go_back())));
			return;
		}

		foreach($requests as $request){
			$profile = Practice::getInstance()
				->getProfileManager()
				->getProfileByXuid($request->getReceiverXuid());
			$this->addButton(TextFormat::WHITE . $profile->getRank()->toColor() . $profile->getPlayerName());
		}

		$this->addButton(Font::bold($translator->translate(CollapseTranslationFactory::form_button_go_back())));
	}
}

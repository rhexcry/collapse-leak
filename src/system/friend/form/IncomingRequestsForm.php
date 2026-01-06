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

final class IncomingRequestsForm extends SimpleForm{

	public function __construct(CollapsePlayer $player){
		$requests = Practice::getInstance()
			->getFriendManager()
			->getIncomingRequests($player->getProfile());
		parent::__construct(function(CollapsePlayer $player, ?int $data = null) use ($requests) : void{
			if($data === null){
				return;
			}

			$request = $requests[$data] ?? null;
			if($request === null){
				$player->sendForm(new FriendsForm($player));
				return;
			}

			$senderName = Practice::getInstance()
				->getProfileManager()
				->getProfileByXuid($request->getSenderXuid())
				->getPlayerName();

			$player->sendForm(new IncomingRequestConcreteForm($player, $senderName));
		});

		$translator = $player->getProfile()->getTranslator();
		$this->setTitle(Font::bold($translator->translate(CollapseTranslationFactory::friend_form_friends_incoming())));

		if(count($requests) === 0){
			$this->setContent(Font::text($translator->translate(CollapseTranslationFactory::friend_form_friends_incoming_no_requests())));
			$this->addButton(Font::bold($translator->translate(CollapseTranslationFactory::form_button_go_back())));
			return;
		}

		foreach($requests as $request){
			$senderName = Practice::getInstance()
				->getProfileManager()
				->getProfileByXuid($request->getSenderXuid())
				->getPlayerName();

			$this->addButton(TextFormat::WHITE . $senderName);
		}

		$this->addButton(Font::bold($translator->translate(CollapseTranslationFactory::form_button_go_back())));
	}
}

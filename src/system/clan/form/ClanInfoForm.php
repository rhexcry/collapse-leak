<?php

declare(strict_types=1);

namespace collapse\system\clan\form;

use collapse\form\CustomForm;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\system\clan\concrete\ClanMember;
use function array_map;
use function implode;

final class ClanInfoForm extends CustomForm{

	public function __construct(CollapsePlayer $player){
		$profile = $player->getProfile();
		parent::__construct(function(CollapsePlayer $player, mixed $data) use ($profile) : void{
			if($data === null){
				return;
			}

		});

		$translator = $profile->getTranslator();
		$clan = $profile->getClan();
		$player->sendMessage('');
		$this->setTitle($translator->translate(CollapseTranslationFactory::command_clan_subarg_info_title($clan->getName())));
		$this->addLabel($translator->translate(CollapseTranslationFactory::command_clan_subarg_info_message(
			$clan->getName(),
			$clan->getTag(),
			Practice::getInstance()->getProfileManager()->getProfileByXuid($clan->getLeaderXuid())->getPlayerName(),
			(string) $clan->getTreasury(),
			(string) $clan->getMemberCount(),
			(string) $clan->getSlots(),
			implode(', ', array_map(function(ClanMember $member){
				return Practice::getInstance()->getProfileManager()->getProfileByXuid($member->getXuid())->getPlayerName();
			}, $clan->getMembers())),
			(string) $clan->getWins(),
			(string) $clan->getLosses(),
			''
		)));
	}
}

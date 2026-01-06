<?php

declare(strict_types=1);

namespace collapse\system\clan\form;

use collapse\form\CustomForm;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\system\clan\ClanConstants;
use function var_dump;

final class EditClanAcceptForm extends CustomForm{

	public function __construct(CollapsePlayer $player, EditClanForm $editForm){
		$profile = $player->getProfile();
		$clan = $profile->getClan();
		$clanManager = Practice::getInstance()->getClanManager();
		parent::__construct(function(CollapsePlayer $player, mixed $data) use ($profile, $clan, $clanManager, $editForm) : void{
			if($data === null){
				return;
			}

			if($profile->getClanId() === null){ //if player was kicked while form opened
				return;
			}

			var_dump($data);
		});

		$latestData = $editForm->getLatestData();

		if($clan->getName() !== $latestData['clanName'] && $clanManager->getClanByName($latestData['clanName']) !== null){
			$this->addLabel('Clan with name exists');
		}

		if($clan->getSlots() === ClanConstants::MAX_SLOTS){

		}

	}
}

<?php

declare(strict_types=1);

namespace collapse\system\clan\form;

use collapse\form\CustomForm;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\system\clan\ClanConstants;

final class EditClanForm extends CustomForm{

	/**
	 * @var array{
	 *     clanName: string,
	 *     clanTag: string,
	 *     maxMembers: int
	 * } $latestData
	 */
	private array $latestData = [];

	public function __construct(CollapsePlayer $player){
		$profile = $player->getProfile();
		$clan = $profile->getClan();
		parent::__construct(function(CollapsePlayer $player, mixed $data) use ($profile, $clan) : void{
			if($data === null){
				return;
			}

			if($profile->getClanId() === null){ //if player was kicked while form opened
				return;
			}

			$this->latestData = [
				'clanName' => $data[0],
				'clanTag' => $data[1],
				'maxMembers' => (int) $data[2],
			];

			$this->resetContentWithLatestData($player);

			$player->sendForm(new EditClanAcceptForm($player, $this));

		});

		$translator = $profile->getTranslator();

		$this->setTitle($translator->translate(CollapseTranslationFactory::clan_form_edit_title($clan->getName())));

		// clan name
		$this->addInput(
			$translator->translate(CollapseTranslationFactory::clan_form_edit_name_label()),
			$translator->translate(CollapseTranslationFactory::clan_form_edit_name_input()),
			$clan->getName()
		);

		// clan tag
		$this->addInput(
			$translator->translate(CollapseTranslationFactory::clan_form_edit_tag_label()),
			$translator->translate(CollapseTranslationFactory::clan_form_edit_tag_input()),
			$clan->getTag()
		);

		// max members
		$this->addSlider(
			$translator->translate(CollapseTranslationFactory::clan_form_edit_maxmembers_label()),
			$clan->getSlots(),
			ClanConstants::MAX_SLOTS
		);
	}

	private function resetContentWithLatestData(CollapsePlayer $player) : void{
		$this->resetContent();

		$translator = $player->getProfile()->getTranslator();
		$clan = $player->getProfile()->getClan();

		$this->setTitle($translator->translate(CollapseTranslationFactory::clan_form_edit_title($clan->getName())));

		// clan name
		$this->addInput(
			$translator->translate(CollapseTranslationFactory::clan_form_edit_name_label()),
			$translator->translate(CollapseTranslationFactory::clan_form_edit_name_input()),
			$this->latestData['clanName']
		);

		// clan tag
		$this->addInput(
			$translator->translate(CollapseTranslationFactory::clan_form_edit_tag_label()),
			$translator->translate(CollapseTranslationFactory::clan_form_edit_tag_input()),
			$this->latestData['clanTag']
		);

		// max members
		$this->addSlider(
			$translator->translate(CollapseTranslationFactory::clan_form_edit_maxmembers_label()),
			$clan->getSlots(),
			ClanConstants::MAX_SLOTS,
			default: $this->latestData['maxMembers']
		);

	}

	public function getLatestData() : array{
		return $this->latestData;
	}
}

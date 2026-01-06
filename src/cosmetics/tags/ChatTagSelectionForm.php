<?php

declare(strict_types=1);

namespace collapse\cosmetics\tags;

use collapse\form\CustomForm;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\resourcepack\Font;
use function array_column;
use function array_map;
use function array_merge;
use function array_search;
use function array_values;

final class ChatTagSelectionForm extends CustomForm{

	public function __construct(CollapsePlayer $player){
		$chatTags = ChatTag::getAvailableChatTags($player);
		parent::__construct(static function(CollapsePlayer $player, ?array $data = null) use ($chatTags) : void{
			if($data === null){
				return;
			}
			$chatTagsManager = Practice::getInstance()->getCosmeticsManager()->getChatTagsManager();
			if($data[0] === 0){
				$chatTagsManager->onChangeChatTag($player->getProfile(), null);
				return;
			}
			$selected = array_values($chatTags)[$data[0] - 1] ?? null;
			if(!$selected instanceof ChatTag){
				return;
			}
			if(!$selected->canUse($player->getProfile())){
				$player->sendTranslatedMessage(CollapseTranslationFactory::cosmetics_cant_equip());
				return;
			}
			$chatTagsManager->onChangeChatTag($player->getProfile(), $selected);
		});
		$translator = $player->getProfile()->getTranslator();
		$this->setTitle(Font::bold($translator->translate(CollapseTranslationFactory::chat_tag_selection_form_title())));
		$selected = array_search(
			$player->getProfile()->getChatTag()?->value ?? '',
			array_column($chatTags, 'value'),
			true
		);
		$this->addDropdown(
			Font::bold($translator->translate(CollapseTranslationFactory::chat_tag_selection_form_dropdown())),
			array_merge(
				[Font::bold($translator->translate(CollapseTranslationFactory::chat_tag_selection_form_none()))],
				array_map(static fn(ChatTag $tag) : string => $tag->toDisplayName(), $chatTags)
			),
			$selected === false ? 0 : $selected + 1
		);
	}
}

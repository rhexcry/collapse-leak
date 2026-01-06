<?php

declare(strict_types=1);

namespace collapse\player\settings;

use collapse\i18n\CollapseTranslationFactory;
use pocketmine\lang\Translatable;

enum Setting : string{

	case AutoSprint = 'auto_sprint';

	case HidePlayersInFreeForAll = 'hide_players_in_free_for_all';

	case PrivateMessages = 'private_messages';

	case FilterObsceneLexis = 'filter_obscene_lexis';

	case HideScoreboard = 'hide_scoreboard';

	case AutoRespawn = 'auto_respawn';

	public function toName() : Translatable{
		return match ($this) {
			self::AutoSprint => CollapseTranslationFactory::setting_auto_sprint(),
			self::HidePlayersInFreeForAll => CollapseTranslationFactory::setting_hide_players_in_free_for_all(),
			self::PrivateMessages => CollapseTranslationFactory::setting_private_messages(),
			self::FilterObsceneLexis => CollapseTranslationFactory::setting_filter_obscene_lexis(),
			self::HideScoreboard => CollapseTranslationFactory::setting_hide_scoreboard(),
			self::AutoRespawn => CollapseTranslationFactory::setting_auto_respawn()
		};
	}

	public function isDefault() : bool{
		return match ($this) {
			self::AutoSprint,
			self::AutoRespawn,
			self::HideScoreboard => false,

			self::PrivateMessages,
			self::HidePlayersInFreeForAll,
			self::FilterObsceneLexis => true
		};
	}
}

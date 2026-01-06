<?php

declare(strict_types=1);

namespace collapse\resourcepack;

use pocketmine\utils\TextFormat;

final readonly class CollapseUI{

	private function __construct(){}

	public const string HEADER_FORM_GRID = TextFormat::MATERIAL_NETHERITE;
	public const string HEADER_FORM_BLACK_TRANSPARENT = '§y';

	public const string GAME_MODE_ICONS = 'textures/ui/collapse/icons/gm';

	public const string FORM_ICONS = 'textures/ui/collapse/icons/form';

	public const string QUESTS_ICONS = 'textures/ui/collapse/icons/quests';

	public const string SCOREBOARD_LOGO = 'collapse.logo';

	public const string UNRANKED_FORM_LOGO = self::FORM_ICONS . '/unranked';
	public const string RANKED_FORM_LOGO = self::FORM_ICONS . '/ranked';
	public const string INCOMING_INVITES_FORM_LOGO = self::FORM_ICONS . '/incoming_invites';
	public const string SPECTATE_FORM_LOGO = self::FORM_ICONS . '/spectate';
	public const string SETTINGS_FORM_LOGO = self::FORM_ICONS . '/settings';
	public const string DUST_EXCHANGE_FORM_LOGO = self::FORM_ICONS . '/dust_exchange';
	public const string FRIENDS_FORM_LOGO = self::FORM_ICONS . '/friends';
	public const string FRIEND_ADD_FORM_LOGO = self::FORM_ICONS . '/friend_add';
	public const string FRIENDS_INCOMING_FORM_LOGO = self::FORM_ICONS . '/friend_incoming';
	public const string FRIENDS_OUTGOING_FORM_LOGO = self::FORM_ICONS . '/friend_outgoing';
	public const string COSMETICS_CHAT_TAGS_FORM_LOGO = self::FORM_ICONS . '/tag';
	public const string COSMETICS_CAPE_FORM_LOGO = self::FORM_ICONS . '/cape';
	public const string COSMETICS_DEATH_EFFECT_FORM_LOGO = self::FORM_ICONS . '/death_effect';
	public const string COSMETICS_POTION_COLORS_FORM_LOGO = self::FORM_ICONS . '/potion_colors';
	public const string PROFILE_UNRANKED_STATISTICS_FORM_LOGO = self::FORM_ICONS . '/profile_unranked_statistics';
	public const string PROFILE_RANKED_STATISTICS_FORM_LOGO = self::FORM_ICONS . '/profile_ranked_statistics';
	public const string PROFILE_FFA_STATISTICS_FORM_LOGO = self::FORM_ICONS . '/profile_ffa_statistics';
	public const string SHOP_RANKS_FORM_LOGO = self::FORM_ICONS . '/ranks';
	public const string KIT_EDITOR_LOGO = self::FORM_ICONS . '/kit_editor';
}

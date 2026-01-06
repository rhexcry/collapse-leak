<?php

declare(strict_types=1);

namespace collapse\i18n;

use pocketmine\lang\Translatable;

/**
 * This class is generated automatically, do NOT modify it by hand.
 *
 * @internal
 */
final class CollapseTranslationFactory{

	public static function anticheat_staff_violation_message(Translatable|string $player, Translatable|string $check, Translatable|string $debug) : Translatable{
		return new Translatable(CollapseTranslationKeys::ANTICHEAT_STAFF_VIOLATION_MESSAGE, [
			'player' => $player,
			'check' => $check,
			'debug' => $debug,
		]);
	}

	public static function ban_broadcast_admins(Translatable|string $player, Translatable|string $sender, Translatable|string $reason, Translatable|string $expiresAt) : Translatable{
		return new Translatable(CollapseTranslationKeys::BAN_BROADCAST_ADMINS, [
			'player' => $player,
			'sender' => $sender,
			'reason' => $reason,
			'expiresAt' => $expiresAt,
		]);
	}

	public static function ban_broadcast_players(Translatable|string $player, Translatable|string $reason) : Translatable{
		return new Translatable(CollapseTranslationKeys::BAN_BROADCAST_PLAYERS, [
			'player' => $player,
			'reason' => $reason,
		]);
	}

	public static function ban_disconnect_screen_message(Translatable|string $bannedName, Translatable|string $reason, Translatable|string $createdAt, Translatable|string $expiresAt) : Translatable{
		return new Translatable(CollapseTranslationKeys::BAN_DISCONNECT_SCREEN_MESSAGE, [
			'bannedName' => $bannedName,
			'reason' => $reason,
			'createdAt' => $createdAt,
			'expiresAt' => $expiresAt,
		]);
	}

	public static function ban_disconnect_screen_message_now(Translatable|string $playerName, Translatable|string $reason, Translatable|string $createdAt, Translatable|string $expiresAt) : Translatable{
		return new Translatable(CollapseTranslationKeys::BAN_DISCONNECT_SCREEN_MESSAGE_NOW, [
			'playerName' => $playerName,
			'reason' => $reason,
			'createdAt' => $createdAt,
			'expiresAt' => $expiresAt,
		]);
	}

	public static function broadcast_social_discord() : Translatable{
		return new Translatable(CollapseTranslationKeys::BROADCAST_SOCIAL_DISCORD, []);
	}

	public static function broadcast_social_site() : Translatable{
		return new Translatable(CollapseTranslationKeys::BROADCAST_SOCIAL_SITE, []);
	}

	public static function broadcast_social_telegram() : Translatable{
		return new Translatable(CollapseTranslationKeys::BROADCAST_SOCIAL_TELEGRAM, []);
	}

	public static function broadcast_social_vk() : Translatable{
		return new Translatable(CollapseTranslationKeys::BROADCAST_SOCIAL_VK, []);
	}

	public static function capes_selection_form_dropdown() : Translatable{
		return new Translatable(CollapseTranslationKeys::CAPES_SELECTION_FORM_DROPDOWN, []);
	}

	public static function capes_selection_form_none() : Translatable{
		return new Translatable(CollapseTranslationKeys::CAPES_SELECTION_FORM_NONE, []);
	}

	public static function capes_selection_form_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::CAPES_SELECTION_FORM_TITLE, []);
	}

	public static function chat_dont_spam(Translatable|string $left) : Translatable{
		return new Translatable(CollapseTranslationKeys::CHAT_DONT_SPAM, [
			'left' => $left,
		]);
	}

	public static function chat_dont_spam_rank(Translatable|string $left, Translatable|string $rank) : Translatable{
		return new Translatable(CollapseTranslationKeys::CHAT_DONT_SPAM_RANK, [
			'left' => $left,
			'rank' => $rank,
		]);
	}

	public static function chat_muted(Translatable|string $reason, Translatable|string $expiresAt) : Translatable{
		return new Translatable(CollapseTranslationKeys::CHAT_MUTED, [
			'reason' => $reason,
			'expiresAt' => $expiresAt,
		]);
	}

	public static function chat_tag_selection_form_dropdown() : Translatable{
		return new Translatable(CollapseTranslationKeys::CHAT_TAG_SELECTION_FORM_DROPDOWN, []);
	}

	public static function chat_tag_selection_form_none() : Translatable{
		return new Translatable(CollapseTranslationKeys::CHAT_TAG_SELECTION_FORM_NONE, []);
	}

	public static function chat_tag_selection_form_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::CHAT_TAG_SELECTION_FORM_TITLE, []);
	}

	public static function clan_form_edit_maxmembers_label() : Translatable{
		return new Translatable(CollapseTranslationKeys::CLAN_FORM_EDIT_MAXMEMBERS_LABEL, []);
	}

	public static function clan_form_edit_name_input() : Translatable{
		return new Translatable(CollapseTranslationKeys::CLAN_FORM_EDIT_NAME_INPUT, []);
	}

	public static function clan_form_edit_name_label() : Translatable{
		return new Translatable(CollapseTranslationKeys::CLAN_FORM_EDIT_NAME_LABEL, []);
	}

	public static function clan_form_edit_tag_input() : Translatable{
		return new Translatable(CollapseTranslationKeys::CLAN_FORM_EDIT_TAG_INPUT, []);
	}

	public static function clan_form_edit_tag_label() : Translatable{
		return new Translatable(CollapseTranslationKeys::CLAN_FORM_EDIT_TAG_LABEL, []);
	}

	public static function clan_form_edit_title(Translatable|string $clanName) : Translatable{
		return new Translatable(CollapseTranslationKeys::CLAN_FORM_EDIT_TITLE, [
			'clanName' => $clanName,
		]);
	}

	public static function command_alts_ban_info(Translatable|string $player, Translatable|string $reason, Translatable|string $bannedBy, Translatable|string $expiresAt) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_ALTS_BAN_INFO, [
			'player' => $player,
			'reason' => $reason,
			'bannedBy' => $bannedBy,
			'expiresAt' => $expiresAt,
		]);
	}

	public static function command_alts_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_ALTS_DESCRIPTION, []);
	}

	public static function command_alts_matches(Translatable|string $player, Translatable|string $ipMatches, Translatable|string $deviceIdMatches) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_ALTS_MATCHES, [
			'player' => $player,
			'ipMatches' => $ipMatches,
			'deviceIdMatches' => $deviceIdMatches,
		]);
	}

	public static function command_alts_mute_info(Translatable|string $player, Translatable|string $reason, Translatable|string $mutedBy, Translatable|string $expiresAt) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_ALTS_MUTE_INFO, [
			'player' => $player,
			'reason' => $reason,
			'mutedBy' => $mutedBy,
			'expiresAt' => $expiresAt,
		]);
	}

	public static function command_alts_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_ALTS_USAGE, []);
	}

	public static function command_ban_already_banned() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_BAN_ALREADY_BANNED, []);
	}

	public static function command_ban_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_BAN_DESCRIPTION, []);
	}

	public static function command_ban_enter_again_for_ban(Translatable|string $player, Translatable|string $reason) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_BAN_ENTER_AGAIN_FOR_BAN, [
			'player' => $player,
			'reason' => $reason,
		]);
	}

	public static function command_ban_successfully(Translatable|string $player, Translatable|string $reason) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_BAN_SUCCESSFULLY, [
			'player' => $player,
			'reason' => $reason,
		]);
	}

	public static function command_ban_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_BAN_USAGE, []);
	}

	public static function command_ban_yourself() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_BAN_YOURSELF, []);
	}

	public static function command_clan_already_has_clan() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_CLAN_ALREADY_HAS_CLAN, []);
	}

	public static function command_clan_clan_with_name_exists(Translatable|string $name) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_CLAN_CLAN_WITH_NAME_EXISTS, [
			'name' => $name,
		]);
	}

	public static function command_clan_clan_with_tag_exists(Translatable|string $tag) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_CLAN_CLAN_WITH_TAG_EXISTS, [
			'tag' => $tag,
		]);
	}

	public static function command_clan_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_CLAN_DESCRIPTION, []);
	}

	public static function command_clan_dont_have_clan() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_CLAN_DONT_HAVE_CLAN, []);
	}

	public static function command_clan_role_too_low() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_CLAN_ROLE_TOO_LOW, []);
	}

	public static function command_clan_subarg_create_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_CLAN_SUBARG_CREATE_USAGE, []);
	}

	public static function command_clan_subarg_info_message(Translatable|string $clanName, Translatable|string $tag, Translatable|string $leaderName, Translatable|string $treasury, Translatable|string $currentMembers, Translatable|string $maxMembers, Translatable|string $memberList, Translatable|string $wins, Translatable|string $losses, Translatable|string $logo) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_CLAN_SUBARG_INFO_MESSAGE, [
			'clanName' => $clanName,
			'tag' => $tag,
			'leaderName' => $leaderName,
			'treasury' => $treasury,
			'currentMembers' => $currentMembers,
			'maxMembers' => $maxMembers,
			'memberList' => $memberList,
			'wins' => $wins,
			'losses' => $losses,
			'logo' => $logo,
		]);
	}

	public static function command_clan_subarg_info_title(Translatable|string $clanName) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_CLAN_SUBARG_INFO_TITLE, [
			'clanName' => $clanName,
		]);
	}

	public static function command_clan_successfully_created(Translatable|string $clanName, Translatable|string $tag) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_CLAN_SUCCESSFULLY_CREATED, [
			'clanName' => $clanName,
			'tag' => $tag,
		]);
	}

	public static function command_clan_successfully_deleted(Translatable|string $clanName) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_CLAN_SUCCESSFULLY_DELETED, [
			'clanName' => $clanName,
		]);
	}

	public static function command_clan_usage(Translatable|string $possibleSubArgs) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_CLAN_USAGE, [
			'possibleSubArgs' => $possibleSubArgs,
		]);
	}

	public static function command_currency_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_CURRENCY_DESCRIPTION, []);
	}

	public static function command_currency_invalid_amount() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_CURRENCY_INVALID_AMOUNT, []);
	}

	public static function command_currency_successfully(Translatable|string $player, Translatable|string $currency, Translatable|string $previous, Translatable|string $balance) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_CURRENCY_SUCCESSFULLY, [
			'player' => $player,
			'currency' => $currency,
			'previous' => $previous,
			'balance' => $balance,
		]);
	}

	public static function command_currency_unknown_action() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_CURRENCY_UNKNOWN_ACTION, []);
	}

	public static function command_currency_unknown_currency() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_CURRENCY_UNKNOWN_CURRENCY, []);
	}

	public static function command_currency_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_CURRENCY_USAGE, []);
	}

	public static function command_duel_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_DUEL_DESCRIPTION, []);
	}

	public static function command_duel_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_DUEL_USAGE, []);
	}

	public static function command_duel_yourself() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_DUEL_YOURSELF, []);
	}

	public static function command_feedback_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_FEEDBACK_DESCRIPTION, []);
	}

	public static function command_feedback_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_FEEDBACK_USAGE, []);
	}

	public static function command_friends_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_FRIENDS_DESCRIPTION, []);
	}

	public static function command_has_cooldown() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_HAS_COOLDOWN, []);
	}

	public static function command_ip_ban_success(Translatable|string $ip, Translatable|string $reason, Translatable|string $expiresAt) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_IP_BAN_SUCCESS, [
			'ip' => $ip,
			'reason' => $reason,
			'expiresAt' => $expiresAt,
		]);
	}

	public static function command_ip_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_IP_DESCRIPTION, []);
	}

	public static function command_ip_invalid_ip() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_IP_INVALID_IP, []);
	}

	public static function command_ip_invalid_subcommand() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_IP_INVALID_SUBCOMMAND, []);
	}

	public static function command_ip_unban_success(Translatable|string $ip) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_IP_UNBAN_SUCCESS, [
			'ip' => $ip,
		]);
	}

	public static function command_ip_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_IP_USAGE, []);
	}

	public static function command_kick_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_KICK_DESCRIPTION, []);
	}

	public static function command_kick_successfully(Translatable|string $player, Translatable|string $reason) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_KICK_SUCCESSFULLY, [
			'player' => $player,
			'reason' => $reason,
		]);
	}

	public static function command_kick_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_KICK_USAGE, []);
	}

	public static function command_kick_yourself() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_KICK_YOURSELF, []);
	}

	public static function command_kit_cancel_not_editing() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_KIT_CANCEL_NOT_EDITING, []);
	}

	public static function command_kit_cancel_successfully(Translatable|string $kit) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_KIT_CANCEL_SUCCESSFULLY, [
			'kit' => $kit,
		]);
	}

	public static function command_kit_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_KIT_DESCRIPTION, []);
	}

	public static function command_kit_reset_in_editing() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_KIT_RESET_IN_EDITING, []);
	}

	public static function command_kit_reset_invalid_kit() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_KIT_RESET_INVALID_KIT, []);
	}

	public static function command_kit_reset_successfully(Translatable|string $kit) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_KIT_RESET_SUCCESSFULLY, [
			'kit' => $kit,
		]);
	}

	public static function command_kit_reset_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_KIT_RESET_USAGE, []);
	}

	public static function command_kit_save_not_editing() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_KIT_SAVE_NOT_EDITING, []);
	}

	public static function command_kit_save_successfully(Translatable|string $kit) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_KIT_SAVE_SUCCESSFULLY, [
			'kit' => $kit,
		]);
	}

	public static function command_kit_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_KIT_USAGE, []);
	}

	public static function command_language_already() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_LANGUAGE_ALREADY, []);
	}

	public static function command_language_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_LANGUAGE_DESCRIPTION, []);
	}

	public static function command_language_language_not_found(Translatable|string $languages) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_LANGUAGE_LANGUAGE_NOT_FOUND, [
			'languages' => $languages,
		]);
	}

	public static function command_language_successfully(Translatable|string $language) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_LANGUAGE_SUCCESSFULLY, [
			'language' => $language,
		]);
	}

	public static function command_language_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_LANGUAGE_USAGE, []);
	}

	public static function command_link_already_exist(Translatable|string $code) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_LINK_ALREADY_EXIST, [
			'code' => $code,
		]);
	}

	public static function command_link_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_LINK_DESCRIPTION, []);
	}

	public static function command_link_success(Translatable|string $code) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_LINK_SUCCESS, [
			'code' => $code,
		]);
	}

	public static function command_link_successfully_linked() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_LINK_SUCCESSFULLY_LINKED, []);
	}

	public static function command_link_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_LINK_USAGE, []);
	}

	public static function command_lobby_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_LOBBY_DESCRIPTION, []);
	}

	public static function command_mp_add(Translatable|string $amount, Translatable|string $playerName) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_MP_ADD, [
			'amount' => $amount,
			'playerName' => $playerName,
		]);
	}

	public static function command_mp_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_MP_DESCRIPTION, []);
	}

	public static function command_mp_invalid_amount() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_MP_INVALID_AMOUNT, []);
	}

	public static function command_mp_resetall(Translatable|string $count) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_MP_RESETALL, [
			'count' => $count,
		]);
	}

	public static function command_mp_stats(Translatable|string $playerName, Translatable|string $weeklyBans, Translatable|string $weeklyMutes, Translatable|string $weeklyKicks, Translatable|string $weeklyMinutes, Translatable|string $weeklyMp, Translatable|string $totalMp) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_MP_STATS, [
			'playerName' => $playerName,
			'weeklyBans' => $weeklyBans,
			'weeklyMutes' => $weeklyMutes,
			'weeklyKicks' => $weeklyKicks,
			'weeklyMinutes' => $weeklyMinutes,
			'weeklyMp' => $weeklyMp,
			'totalMp' => $totalMp,
		]);
	}

	public static function command_mp_subtract(Translatable|string $amount, Translatable|string $playerName) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_MP_SUBTRACT, [
			'amount' => $amount,
			'playerName' => $playerName,
		]);
	}

	public static function command_mp_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_MP_USAGE, []);
	}

	public static function command_mute_already_muted() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_MUTE_ALREADY_MUTED, []);
	}

	public static function command_mute_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_MUTE_DESCRIPTION, []);
	}

	public static function command_mute_enter_again_for_mute(Translatable|string $player, Translatable|string $reason) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_MUTE_ENTER_AGAIN_FOR_MUTE, [
			'player' => $player,
			'reason' => $reason,
		]);
	}

	public static function command_mute_successfully(Translatable|string $player, Translatable|string $reason) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_MUTE_SUCCESSFULLY, [
			'player' => $player,
			'reason' => $reason,
		]);
	}

	public static function command_mute_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_MUTE_USAGE, []);
	}

	public static function command_mute_yourself() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_MUTE_YOURSELF, []);
	}

	public static function command_no_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_NO_DESCRIPTION, []);
	}

	public static function command_observe_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_OBSERVE_DESCRIPTION, []);
	}

	public static function command_observe_start(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_OBSERVE_START, [
			'player' => $player,
		]);
	}

	public static function command_observe_stop(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_OBSERVE_STOP, [
			'player' => $player,
		]);
	}

	public static function command_observe_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_OBSERVE_USAGE, []);
	}

	public static function command_online_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_ONLINE_DESCRIPTION, []);
	}

	public static function command_online_message(Translatable|string $count, Translatable|string $list) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_ONLINE_MESSAGE, [
			'count' => $count,
			'list' => $list,
		]);
	}

	public static function command_party_already_in_party() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_PARTY_ALREADY_IN_PARTY, []);
	}

	public static function command_party_args_invite_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_PARTY_ARGS_INVITE_USAGE, []);
	}

	public static function command_party_created() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_PARTY_CREATED, []);
	}

	public static function command_party_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_PARTY_DESCRIPTION, []);
	}

	public static function command_party_disbanded() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_PARTY_DISBANDED, []);
	}

	public static function command_party_dont_have_invite_in_party(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_PARTY_DONT_HAVE_INVITE_IN_PARTY, [
			'player' => $player,
		]);
	}

	public static function command_party_info_message(Translatable|string $leader, Translatable|string $members) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_PARTY_INFO_MESSAGE, [
			'leader' => $leader,
			'members' => $members,
		]);
	}

	public static function command_party_joined() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_PARTY_JOINED, []);
	}

	public static function command_party_joined_members(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_PARTY_JOINED_MEMBERS, [
			'player' => $player,
		]);
	}

	public static function command_party_leave_cant_leader() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_PARTY_LEAVE_CANT_LEADER, []);
	}

	public static function command_party_left() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_PARTY_LEFT, []);
	}

	public static function command_party_left_members(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_PARTY_LEFT_MEMBERS, [
			'player' => $player,
		]);
	}

	public static function command_party_not_in_party() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_PARTY_NOT_IN_PARTY, []);
	}

	public static function command_party_not_leader() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_PARTY_NOT_LEADER, []);
	}

	public static function command_party_player_already_in_party(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_PARTY_PLAYER_ALREADY_IN_PARTY, [
			'player' => $player,
		]);
	}

	public static function command_party_player_dont_have_party(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_PARTY_PLAYER_DONT_HAVE_PARTY, [
			'player' => $player,
		]);
	}

	public static function command_party_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_PARTY_USAGE, []);
	}

	public static function command_ping_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_PING_DESCRIPTION, []);
	}

	public static function command_ping_other_player(Translatable|string $player, Translatable|string $ping) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_PING_OTHER_PLAYER, [
			'player' => $player,
			'ping' => $ping,
		]);
	}

	public static function command_ping_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_PING_USAGE, []);
	}

	public static function command_ping_yourself(Translatable|string $ping) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_PING_YOURSELF, [
			'ping' => $ping,
		]);
	}

	public static function command_profile_basic(Translatable|string $player, Translatable|string $status, Translatable|string $rank, Translatable|string $version, Translatable|string $firstJoin, Translatable|string $inputMode, Translatable|string $deviceOS) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_PROFILE_BASIC, [
			'player' => $player,
			'status' => $status,
			'rank' => $rank,
			'version' => $version,
			'firstJoin' => $firstJoin,
			'inputMode' => $inputMode,
			'deviceOS' => $deviceOS,
		]);
	}

	public static function command_profile_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_PROFILE_DESCRIPTION, []);
	}

	public static function command_profile_staff_advanced(Translatable|string $player, Translatable|string $status, Translatable|string $rank, Translatable|string $version, Translatable|string $firstJoin, Translatable|string $inputMode, Translatable|string $deviceOS, Translatable|string $deviceModel) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_PROFILE_STAFF_ADVANCED, [
			'player' => $player,
			'status' => $status,
			'rank' => $rank,
			'version' => $version,
			'firstJoin' => $firstJoin,
			'inputMode' => $inputMode,
			'deviceOS' => $deviceOS,
			'deviceModel' => $deviceModel,
		]);
	}

	public static function command_profile_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_PROFILE_USAGE, []);
	}

	public static function command_quest_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_QUEST_DESCRIPTION, []);
	}

	public static function command_re_kit_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_RE_KIT_DESCRIPTION, []);
	}

	public static function command_re_kit_in_combat() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_RE_KIT_IN_COMBAT, []);
	}

	public static function command_re_kit_not_in_arena() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_RE_KIT_NOT_IN_ARENA, []);
	}

	public static function command_re_kit_respawning() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_RE_KIT_RESPAWNING, []);
	}

	public static function command_re_kit_successfully() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_RE_KIT_SUCCESSFULLY, []);
	}

	public static function command_re_kit_unavailable() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_RE_KIT_UNAVAILABLE, []);
	}

	public static function command_reply_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_REPLY_DESCRIPTION, []);
	}

	public static function command_reply_nobody() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_REPLY_NOBODY, []);
	}

	public static function command_reply_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_REPLY_USAGE, []);
	}

	public static function command_report_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_REPORT_DESCRIPTION, []);
	}

	public static function command_report_successfully(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_REPORT_SUCCESSFULLY, [
			'player' => $player,
		]);
	}

	public static function command_report_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_REPORT_USAGE, []);
	}

	public static function command_reports_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_REPORTS_DESCRIPTION, []);
	}

	public static function command_restart_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_RESTART_DESCRIPTION, []);
	}

	public static function command_restart_remaining(Translatable|string $remaining) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_RESTART_REMAINING, [
			'remaining' => $remaining,
		]);
	}

	public static function command_restart_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_RESTART_USAGE, []);
	}

	public static function command_set_rank_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_SET_RANK_DESCRIPTION, []);
	}

	public static function command_set_rank_rank_not_found(Translatable|string $possibleRanks) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_SET_RANK_RANK_NOT_FOUND, [
			'possibleRanks' => $possibleRanks,
		]);
	}

	public static function command_set_rank_successfully(Translatable|string $player, Translatable|string $rank) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_SET_RANK_SUCCESSFULLY, [
			'player' => $player,
			'rank' => $rank,
		]);
	}

	public static function command_set_rank_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_SET_RANK_USAGE, []);
	}

	public static function command_showcoordinates_changed() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_SHOWCOORDINATES_CHANGED, []);
	}

	public static function command_showcoordinates_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_SHOWCOORDINATES_DESCRIPTION, []);
	}

	public static function command_showcoordinates_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_SHOWCOORDINATES_USAGE, []);
	}

	public static function command_spectate_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_SPECTATE_DESCRIPTION, []);
	}

	public static function command_spectate_not_in_lobby() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_SPECTATE_NOT_IN_LOBBY, []);
	}

	public static function command_spectate_successfully(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_SPECTATE_SUCCESSFULLY, [
			'player' => $player,
		]);
	}

	public static function command_spectate_target_not_in_game(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_SPECTATE_TARGET_NOT_IN_GAME, [
			'player' => $player,
		]);
	}

	public static function command_spectate_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_SPECTATE_USAGE, []);
	}

	public static function command_tell_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_TELL_DESCRIPTION, []);
	}

	public static function command_tell_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_TELL_USAGE, []);
	}

	public static function command_tell_yourself() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_TELL_YOURSELF, []);
	}

	public static function command_unban_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_UNBAN_DESCRIPTION, []);
	}

	public static function command_unban_not_banned() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_UNBAN_NOT_BANNED, []);
	}

	public static function command_unban_successfully(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_UNBAN_SUCCESSFULLY, [
			'player' => $player,
		]);
	}

	public static function command_unban_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_UNBAN_USAGE, []);
	}

	public static function command_unmute_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_UNMUTE_DESCRIPTION, []);
	}

	public static function command_unmute_not_muted() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_UNMUTE_NOT_MUTED, []);
	}

	public static function command_unmute_successfully(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_UNMUTE_SUCCESSFULLY, [
			'player' => $player,
		]);
	}

	public static function command_unmute_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_UNMUTE_USAGE, []);
	}

	public static function command_world_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_WORLD_DESCRIPTION, []);
	}

	public static function command_world_save_success(Translatable|string $world) : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_WORLD_SAVE_SUCCESS, [
			'world' => $world,
		]);
	}

	public static function command_world_unknown_world() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_WORLD_UNKNOWN_WORLD, []);
	}

	public static function command_world_usage() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_WORLD_USAGE, []);
	}

	public static function command_yes_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::COMMAND_YES_DESCRIPTION, []);
	}

	public static function cooldown_ender_pearl_active() : Translatable{
		return new Translatable(CollapseTranslationKeys::COOLDOWN_ENDER_PEARL_ACTIVE, []);
	}

	public static function cooldown_ender_pearl_ended() : Translatable{
		return new Translatable(CollapseTranslationKeys::COOLDOWN_ENDER_PEARL_ENDED, []);
	}

	public static function cooldown_ender_pearl_started() : Translatable{
		return new Translatable(CollapseTranslationKeys::COOLDOWN_ENDER_PEARL_STARTED, []);
	}

	public static function cosmetics_cant_equip() : Translatable{
		return new Translatable(CollapseTranslationKeys::COSMETICS_CANT_EQUIP, []);
	}

	public static function cosmetics_form_button_capes() : Translatable{
		return new Translatable(CollapseTranslationKeys::COSMETICS_FORM_BUTTON_CAPES, []);
	}

	public static function cosmetics_form_button_death_effects() : Translatable{
		return new Translatable(CollapseTranslationKeys::COSMETICS_FORM_BUTTON_DEATH_EFFECTS, []);
	}

	public static function cosmetics_form_button_potion_colors() : Translatable{
		return new Translatable(CollapseTranslationKeys::COSMETICS_FORM_BUTTON_POTION_COLORS, []);
	}

	public static function cosmetics_form_button_tags() : Translatable{
		return new Translatable(CollapseTranslationKeys::COSMETICS_FORM_BUTTON_TAGS, []);
	}

	public static function cosmetics_form_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::COSMETICS_FORM_TITLE, []);
	}

	public static function death_effect_form_none() : Translatable{
		return new Translatable(CollapseTranslationKeys::DEATH_EFFECT_FORM_NONE, []);
	}

	public static function death_effect_selection_form_dropdown() : Translatable{
		return new Translatable(CollapseTranslationKeys::DEATH_EFFECT_SELECTION_FORM_DROPDOWN, []);
	}

	public static function death_effect_selection_form_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::DEATH_EFFECT_SELECTION_FORM_TITLE, []);
	}

	public static function drop_main_weapon() : Translatable{
		return new Translatable(CollapseTranslationKeys::DROP_MAIN_WEAPON, []);
	}

	public static function duel_from_declined(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUEL_FROM_DECLINED, [
			'player' => $player,
		]);
	}

	public static function duels_base_bed_destroyed(Translatable|string $color, Translatable|string $team, Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_BASE_BED_DESTROYED, [
			'color' => $color,
			'team' => $team,
			'player' => $player,
		]);
	}

	public static function duels_base_bed_destroyed_subtitle() : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_BASE_BED_DESTROYED_SUBTITLE, []);
	}

	public static function duels_base_bed_destroyed_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_BASE_BED_DESTROYED_TITLE, []);
	}

	public static function duels_base_bed_scoreboard_alive(Translatable|string $color, Translatable|string $team, Translatable|string $your) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_BASE_BED_SCOREBOARD_ALIVE, [
			'color' => $color,
			'team' => $team,
			'your' => $your,
		]);
	}

	public static function duels_base_bed_scoreboard_destroyed(Translatable|string $color, Translatable|string $team, Translatable|string $alivePlayers, Translatable|string $your) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_BASE_BED_SCOREBOARD_DESTROYED, [
			'color' => $color,
			'team' => $team,
			'alivePlayers' => $alivePlayers,
			'your' => $your,
		]);
	}

	public static function duels_base_beds_destroy_self() : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_BASE_BEDS_DESTROY_SELF, []);
	}

	public static function duels_base_scoreboard_your_team() : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_BASE_SCOREBOARD_YOUR_TEAM, []);
	}

	public static function duels_elo_updates(Translatable|string $winners, Translatable|string $losers) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_ELO_UPDATES, [
			'winners' => $winners,
			'losers' => $losers,
		]);
	}

	public static function duels_form_button_incoming_invites() : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_FORM_BUTTON_INCOMING_INVITES, []);
	}

	public static function duels_form_button_ranked(Translatable|string $queue, Translatable|string $playing) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_FORM_BUTTON_RANKED, [
			'queue' => $queue,
			'playing' => $playing,
		]);
	}

	public static function duels_form_button_ranked_mode(Translatable|string $mode, Translatable|string $queue, Translatable|string $playing) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_FORM_BUTTON_RANKED_MODE, [
			'mode' => $mode,
			'queue' => $queue,
			'playing' => $playing,
		]);
	}

	public static function duels_form_button_spectate_duels() : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_FORM_BUTTON_SPECTATE_DUELS, []);
	}

	public static function duels_form_button_unranked(Translatable|string $queue, Translatable|string $playing) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_FORM_BUTTON_UNRANKED, [
			'queue' => $queue,
			'playing' => $playing,
		]);
	}

	public static function duels_form_button_unranked_mode(Translatable|string $mode, Translatable|string $queue, Translatable|string $playing) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_FORM_BUTTON_UNRANKED_MODE, [
			'mode' => $mode,
			'queue' => $queue,
			'playing' => $playing,
		]);
	}

	public static function duels_form_no_incoming_invites() : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_FORM_NO_INCOMING_INVITES, []);
	}

	public static function duels_form_request_title(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_FORM_REQUEST_TITLE, [
			'player' => $player,
		]);
	}

	public static function duels_form_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_FORM_TITLE, []);
	}

	public static function duels_incoming_invites_form_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_INCOMING_INVITES_FORM_TITLE, []);
	}

	public static function duels_item_block_in_queue() : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_ITEM_BLOCK_IN_QUEUE, []);
	}

	public static function duels_item_fireball() : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_ITEM_FIREBALL, []);
	}

	public static function duels_item_golden_head() : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_ITEM_GOLDEN_HEAD, []);
	}

	public static function duels_item_leave_queue() : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_ITEM_LEAVE_QUEUE, []);
	}

	public static function duels_item_stop_spectating() : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_ITEM_STOP_SPECTATING, []);
	}

	public static function duels_item_tnt() : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_ITEM_TNT, []);
	}

	public static function duels_match_results(Translatable|string $winner, Translatable|string $loser) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_MATCH_RESULTS, [
			'winner' => $winner,
			'loser' => $loser,
		]);
	}

	public static function duels_match_started() : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_MATCH_STARTED, []);
	}

	public static function duels_no_maps() : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_NO_MAPS, []);
	}

	public static function duels_phase_countdown_scoreboard_opponent() : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_PHASE_COUNTDOWN_SCOREBOARD_OPPONENT, []);
	}

	public static function duels_phase_countdown_scoreboard_opponent_info(Translatable|string $opponent, Translatable|string $ping) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_PHASE_COUNTDOWN_SCOREBOARD_OPPONENT_INFO, [
			'opponent' => $opponent,
			'ping' => $ping,
		]);
	}

	public static function duels_phase_countdown_scoreboard_team(Translatable|string $team) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_PHASE_COUNTDOWN_SCOREBOARD_TEAM, [
			'team' => $team,
		]);
	}

	public static function duels_phase_countdown_scoreboard_team_player_info_alive(Translatable|string $player, Translatable|string $ping) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_PHASE_COUNTDOWN_SCOREBOARD_TEAM_PLAYER_INFO_ALIVE, [
			'player' => $player,
			'ping' => $ping,
		]);
	}

	public static function duels_phase_countdown_scoreboard_team_player_info_died(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_PHASE_COUNTDOWN_SCOREBOARD_TEAM_PLAYER_INFO_DIED, [
			'player' => $player,
		]);
	}

	public static function duels_phase_countdown_scoreboard_your_ping(Translatable|string $ping) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_PHASE_COUNTDOWN_SCOREBOARD_YOUR_PING, [
			'ping' => $ping,
		]);
	}

	public static function duels_phase_end_scoreboard_defeat() : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_PHASE_END_SCOREBOARD_DEFEAT, []);
	}

	public static function duels_phase_end_scoreboard_duration(Translatable|string $duration) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_PHASE_END_SCOREBOARD_DURATION, [
			'duration' => $duration,
		]);
	}

	public static function duels_phase_end_scoreboard_victory() : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_PHASE_END_SCOREBOARD_VICTORY, []);
	}

	public static function duels_phase_running_scoreboard_opponent() : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_PHASE_RUNNING_SCOREBOARD_OPPONENT, []);
	}

	public static function duels_phase_running_scoreboard_opponent_info(Translatable|string $opponent, Translatable|string $ping) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_PHASE_RUNNING_SCOREBOARD_OPPONENT_INFO, [
			'opponent' => $opponent,
			'ping' => $ping,
		]);
	}

	public static function duels_phase_running_scoreboard_your_ping(Translatable|string $ping) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_PHASE_RUNNING_SCOREBOARD_YOUR_PING, [
			'ping' => $ping,
		]);
	}

	public static function duels_post_match_form_button_player(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_POST_MATCH_FORM_BUTTON_PLAYER, [
			'player' => $player,
		]);
	}

	public static function duels_post_match_form_button_queue_again() : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_POST_MATCH_FORM_BUTTON_QUEUE_AGAIN, []);
	}

	public static function duels_post_match_form_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_POST_MATCH_FORM_TITLE, []);
	}

	public static function duels_post_match_inventory_item_block_in_queue() : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_POST_MATCH_INVENTORY_ITEM_BLOCK_IN_QUEUE, []);
	}

	public static function duels_post_match_inventory_item_combat(Translatable|string $hits, Translatable|string $criticalHits, Translatable|string $maxCombo) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_POST_MATCH_INVENTORY_ITEM_COMBAT, [
			'hits' => $hits,
			'criticalHits' => $criticalHits,
			'maxCombo' => $maxCombo,
		]);
	}

	public static function duels_post_match_inventory_item_health(Translatable|string $health) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_POST_MATCH_INVENTORY_ITEM_HEALTH, [
			'health' => $health,
		]);
	}

	public static function duels_post_match_inventory_item_hunger(Translatable|string $hunger) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_POST_MATCH_INVENTORY_ITEM_HUNGER, [
			'hunger' => $hunger,
		]);
	}

	public static function duels_post_match_inventory_item_potion_effects(Translatable|string $potionEffects) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_POST_MATCH_INVENTORY_ITEM_POTION_EFFECTS, [
			'potionEffects' => $potionEffects,
		]);
	}

	public static function duels_post_match_inventory_item_view(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_POST_MATCH_INVENTORY_ITEM_VIEW, [
			'player' => $player,
		]);
	}

	public static function duels_post_match_inventory_name(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_POST_MATCH_INVENTORY_NAME, [
			'player' => $player,
		]);
	}

	public static function duels_ranked_statistics_form_button(Translatable|string $mode, Translatable|string $elo, Translatable|string $bestElo, Translatable|string $wins, Translatable|string $kills, Translatable|string $plays, Translatable|string $winrate, Translatable|string $winstreak, Translatable|string $bestWinstreak) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_RANKED_STATISTICS_FORM_BUTTON, [
			'mode' => $mode,
			'elo' => $elo,
			'bestElo' => $bestElo,
			'wins' => $wins,
			'kills' => $kills,
			'plays' => $plays,
			'winrate' => $winrate,
			'winstreak' => $winstreak,
			'bestWinstreak' => $bestWinstreak,
		]);
	}

	public static function duels_requests_already_sent() : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_REQUESTS_ALREADY_SENT, []);
	}

	public static function duels_requests_members_not_in_lobby(Translatable|string $names) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_REQUESTS_MEMBERS_NOT_IN_LOBBY, [
			'names' => $names,
		]);
	}

	public static function duels_requests_players_not_in_lobby(Translatable|string $names) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_REQUESTS_PLAYERS_NOT_IN_LOBBY, [
			'names' => $names,
		]);
	}

	public static function duels_requests_receive(Translatable|string $mode, Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_REQUESTS_RECEIVE, [
			'mode' => $mode,
			'player' => $player,
		]);
	}

	public static function duels_requests_successfully(Translatable|string $mode, Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_REQUESTS_SUCCESSFULLY, [
			'mode' => $mode,
			'player' => $player,
		]);
	}

	public static function duels_spectate_form_button(Translatable|string $player1, Translatable|string $player2, Translatable|string $mode) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_SPECTATE_FORM_BUTTON, [
			'player1' => $player1,
			'player2' => $player2,
			'mode' => $mode,
		]);
	}

	public static function duels_spectate_form_no_duels() : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_SPECTATE_FORM_NO_DUELS, []);
	}

	public static function duels_spectate_form_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_SPECTATE_FORM_TITLE, []);
	}

	public static function duels_started_spectating(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_STARTED_SPECTATING, [
			'player' => $player,
		]);
	}

	public static function duels_statistics_form_title(Translatable|string $type) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_STATISTICS_FORM_TITLE, [
			'type' => $type,
		]);
	}

	public static function duels_stopped_spectating(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_STOPPED_SPECTATING, [
			'player' => $player,
		]);
	}

	public static function duels_unranked_statistics_form_button(Translatable|string $mode, Translatable|string $wins, Translatable|string $kills, Translatable|string $plays, Translatable|string $winrate, Translatable|string $winstreak, Translatable|string $bestWinstreak) : Translatable{
		return new Translatable(CollapseTranslationKeys::DUELS_UNRANKED_STATISTICS_FORM_BUTTON, [
			'mode' => $mode,
			'wins' => $wins,
			'kills' => $kills,
			'plays' => $plays,
			'winrate' => $winrate,
			'winstreak' => $winstreak,
			'bestWinstreak' => $bestWinstreak,
		]);
	}

	public static function exchange_amount_form_incorrect_input() : Translatable{
		return new Translatable(CollapseTranslationKeys::EXCHANGE_AMOUNT_FORM_INCORRECT_INPUT, []);
	}

	public static function exchange_amount_form_input() : Translatable{
		return new Translatable(CollapseTranslationKeys::EXCHANGE_AMOUNT_FORM_INPUT, []);
	}

	public static function exchange_form_button_all() : Translatable{
		return new Translatable(CollapseTranslationKeys::EXCHANGE_FORM_BUTTON_ALL, []);
	}

	public static function exchange_form_button_amount() : Translatable{
		return new Translatable(CollapseTranslationKeys::EXCHANGE_FORM_BUTTON_AMOUNT, []);
	}

	public static function exchange_form_content() : Translatable{
		return new Translatable(CollapseTranslationKeys::EXCHANGE_FORM_CONTENT, []);
	}

	public static function exchange_form_not_enough_dust() : Translatable{
		return new Translatable(CollapseTranslationKeys::EXCHANGE_FORM_NOT_ENOUGH_DUST, []);
	}

	public static function exchange_form_successfully(Translatable|string $dust, Translatable|string $star) : Translatable{
		return new Translatable(CollapseTranslationKeys::EXCHANGE_FORM_SUCCESSFULLY, [
			'dust' => $dust,
			'star' => $star,
		]);
	}

	public static function exchange_form_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::EXCHANGE_FORM_TITLE, []);
	}

	public static function feature_unavailable() : Translatable{
		return new Translatable(CollapseTranslationKeys::FEATURE_UNAVAILABLE, []);
	}

	public static function form_button_go_back() : Translatable{
		return new Translatable(CollapseTranslationKeys::FORM_BUTTON_GO_BACK, []);
	}

	public static function free_for_all_combat_scoreboard_combat_time(Translatable|string $param0) : Translatable{
		return new Translatable(CollapseTranslationKeys::FREE_FOR_ALL_COMBAT_SCOREBOARD_COMBAT_TIME, [
			0 => $param0,
		]);
	}

	public static function free_for_all_combat_scoreboard_opponent() : Translatable{
		return new Translatable(CollapseTranslationKeys::FREE_FOR_ALL_COMBAT_SCOREBOARD_OPPONENT, []);
	}

	public static function free_for_all_combat_scoreboard_opponent_info(Translatable|string $opponent, Translatable|string $ping) : Translatable{
		return new Translatable(CollapseTranslationKeys::FREE_FOR_ALL_COMBAT_SCOREBOARD_OPPONENT_INFO, [
			'opponent' => $opponent,
			'ping' => $ping,
		]);
	}

	public static function free_for_all_combat_scoreboard_your_ping(Translatable|string $ping) : Translatable{
		return new Translatable(CollapseTranslationKeys::FREE_FOR_ALL_COMBAT_SCOREBOARD_YOUR_PING, [
			'ping' => $ping,
		]);
	}

	public static function free_for_all_entered_combat(Translatable|string $param0) : Translatable{
		return new Translatable(CollapseTranslationKeys::FREE_FOR_ALL_ENTERED_COMBAT, [
			0 => $param0,
		]);
	}

	public static function free_for_all_form_button(Translatable|string $mode, Translatable|string $playing) : Translatable{
		return new Translatable(CollapseTranslationKeys::FREE_FOR_ALL_FORM_BUTTON, [
			'mode' => $mode,
			'playing' => $playing,
		]);
	}

	public static function free_for_all_in_combat(Translatable|string $player1, Translatable|string $player2) : Translatable{
		return new Translatable(CollapseTranslationKeys::FREE_FOR_ALL_IN_COMBAT, [
			'player1' => $player1,
			'player2' => $player2,
		]);
	}

	public static function free_for_all_item_lobby() : Translatable{
		return new Translatable(CollapseTranslationKeys::FREE_FOR_ALL_ITEM_LOBBY, []);
	}

	public static function free_for_all_item_respawn() : Translatable{
		return new Translatable(CollapseTranslationKeys::FREE_FOR_ALL_ITEM_RESPAWN, []);
	}

	public static function free_for_all_joined(Translatable|string $param0) : Translatable{
		return new Translatable(CollapseTranslationKeys::FREE_FOR_ALL_JOINED, [
			0 => $param0,
		]);
	}

	public static function free_for_all_respawn_after(Translatable|string $remaining) : Translatable{
		return new Translatable(CollapseTranslationKeys::FREE_FOR_ALL_RESPAWN_AFTER, [
			'remaining' => $remaining,
		]);
	}

	public static function free_for_all_respawned() : Translatable{
		return new Translatable(CollapseTranslationKeys::FREE_FOR_ALL_RESPAWNED, []);
	}

	public static function free_for_all_scoreboard_mode(Translatable|string $param0) : Translatable{
		return new Translatable(CollapseTranslationKeys::FREE_FOR_ALL_SCOREBOARD_MODE, [
			0 => $param0,
		]);
	}

	public static function free_for_all_scoreboard_playing(Translatable|string $param0) : Translatable{
		return new Translatable(CollapseTranslationKeys::FREE_FOR_ALL_SCOREBOARD_PLAYING, [
			0 => $param0,
		]);
	}

	public static function free_for_all_scoreboard_your_ping(Translatable|string $ping) : Translatable{
		return new Translatable(CollapseTranslationKeys::FREE_FOR_ALL_SCOREBOARD_YOUR_PING, [
			'ping' => $ping,
		]);
	}

	public static function free_for_all_statistics_form_button(Translatable|string $mode, Translatable|string $kills, Translatable|string $deaths, Translatable|string $kd, Translatable|string $killstreak, Translatable|string $bestKillstreak) : Translatable{
		return new Translatable(CollapseTranslationKeys::FREE_FOR_ALL_STATISTICS_FORM_BUTTON, [
			'mode' => $mode,
			'kills' => $kills,
			'deaths' => $deaths,
			'kd' => $kd,
			'killstreak' => $killstreak,
			'bestKillstreak' => $bestKillstreak,
		]);
	}

	public static function free_for_all_statistics_form_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::FREE_FOR_ALL_STATISTICS_FORM_TITLE, []);
	}

	public static function friend_cant_request_itself() : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIEND_CANT_REQUEST_ITSELF, []);
	}

	public static function friend_deleted(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIEND_DELETED, [
			'player' => $player,
		]);
	}

	public static function friend_form_add_friend_playerName_input() : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIEND_FORM_ADD_FRIEND_PLAYERNAME_INPUT, []);
	}

	public static function friend_form_add_friend_playerName_label() : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIEND_FORM_ADD_FRIEND_PLAYERNAME_LABEL, []);
	}

	public static function friend_form_friends_add() : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIEND_FORM_FRIENDS_ADD, []);
	}

	public static function friend_form_friends_incoming() : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIEND_FORM_FRIENDS_INCOMING, []);
	}

	public static function friend_form_friends_incoming_no_requests() : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIEND_FORM_FRIENDS_INCOMING_NO_REQUESTS, []);
	}

	public static function friend_form_friends_my_friends() : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIEND_FORM_FRIENDS_MY_FRIENDS, []);
	}

	public static function friend_form_friends_no_friends() : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIEND_FORM_FRIENDS_NO_FRIENDS, []);
	}

	public static function friend_form_friends_outgoing() : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIEND_FORM_FRIENDS_OUTGOING, []);
	}

	public static function friend_form_friends_outgoing_no_requests() : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIEND_FORM_FRIENDS_OUTGOING_NO_REQUESTS, []);
	}

	public static function friend_form_friends_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIEND_FORM_FRIENDS_TITLE, []);
	}

	public static function friend_form_incoming_request_concrete_accept() : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIEND_FORM_INCOMING_REQUEST_CONCRETE_ACCEPT, []);
	}

	public static function friend_form_incoming_request_concrete_decline() : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIEND_FORM_INCOMING_REQUEST_CONCRETE_DECLINE, []);
	}

	public static function friend_form_my_friends_concrete_delete() : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIEND_FORM_MY_FRIENDS_CONCRETE_DELETE, []);
	}

	public static function friend_form_outgoing_request_cancel() : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIEND_FORM_OUTGOING_REQUEST_CANCEL, []);
	}

	public static function friend_request_accepted(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIEND_REQUEST_ACCEPTED, [
			'player' => $player,
		]);
	}

	public static function friend_request_accepted_to(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIEND_REQUEST_ACCEPTED_TO, [
			'player' => $player,
		]);
	}

	public static function friend_request_cancelled() : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIEND_REQUEST_CANCELLED, []);
	}

	public static function friend_request_declined(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIEND_REQUEST_DECLINED, [
			'player' => $player,
		]);
	}

	public static function friend_request_max_friends_exceeded() : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIEND_REQUEST_MAX_FRIENDS_EXCEEDED, []);
	}

	public static function friend_request_user_max_friends_exceeded(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIEND_REQUEST_USER_MAX_FRIENDS_EXCEEDED, [
			'player' => $player,
		]);
	}

	public static function friends_player_already_added(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIENDS_PLAYER_ALREADY_ADDED, [
			'player' => $player,
		]);
	}

	public static function friends_request_already_sent(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIENDS_REQUEST_ALREADY_SENT, [
			'player' => $player,
		]);
	}

	public static function friends_request_new_incoming(Translatable|string $sender) : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIENDS_REQUEST_NEW_INCOMING, [
			'sender' => $sender,
		]);
	}

	public static function friends_request_successfully_sent(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIENDS_REQUEST_SUCCESSFULLY_SENT, [
			'player' => $player,
		]);
	}

	public static function friends_self_add() : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIENDS_SELF_ADD, []);
	}

	public static function friends_status_accepted() : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIENDS_STATUS_ACCEPTED, []);
	}

	public static function friends_status_declined() : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIENDS_STATUS_DECLINED, []);
	}

	public static function friends_status_pending() : Translatable{
		return new Translatable(CollapseTranslationKeys::FRIENDS_STATUS_PENDING, []);
	}

	public static function game_statistics_critical_hits() : Translatable{
		return new Translatable(CollapseTranslationKeys::GAME_STATISTICS_CRITICAL_HITS, []);
	}

	public static function game_statistics_damage_dealt() : Translatable{
		return new Translatable(CollapseTranslationKeys::GAME_STATISTICS_DAMAGE_DEALT, []);
	}

	public static function game_statistics_health_regenerated() : Translatable{
		return new Translatable(CollapseTranslationKeys::GAME_STATISTICS_HEALTH_REGENERATED, []);
	}

	public static function game_statistics_hits() : Translatable{
		return new Translatable(CollapseTranslationKeys::GAME_STATISTICS_HITS, []);
	}

	public static function game_statistics_max_combo() : Translatable{
		return new Translatable(CollapseTranslationKeys::GAME_STATISTICS_MAX_COMBO, []);
	}

	public static function game_statistics_throw_potions() : Translatable{
		return new Translatable(CollapseTranslationKeys::GAME_STATISTICS_THROW_POTIONS, []);
	}

	public static function game_statistics_title(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::GAME_STATISTICS_TITLE, [
			'player' => $player,
		]);
	}

	public static function kick_broadcast_admins(Translatable|string $player, Translatable|string $sender, Translatable|string $reason) : Translatable{
		return new Translatable(CollapseTranslationKeys::KICK_BROADCAST_ADMINS, [
			'player' => $player,
			'sender' => $sender,
			'reason' => $reason,
		]);
	}

	public static function kick_broadcast_players(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::KICK_BROADCAST_PLAYERS, [
			'player' => $player,
		]);
	}

	public static function kick_disconnect_screen_message(Translatable|string $reason) : Translatable{
		return new Translatable(CollapseTranslationKeys::KICK_DISCONNECT_SCREEN_MESSAGE, [
			'reason' => $reason,
		]);
	}

	public static function kill_messages_default_explode(Translatable|string $player, Translatable|string $killer) : Translatable{
		return new Translatable(CollapseTranslationKeys::KILL_MESSAGES_DEFAULT_EXPLODE, [
			'player' => $player,
			'killer' => $killer,
		]);
	}

	public static function kill_messages_default_player(Translatable|string $player, Translatable|string $killer) : Translatable{
		return new Translatable(CollapseTranslationKeys::KILL_MESSAGES_DEFAULT_PLAYER, [
			'player' => $player,
			'killer' => $killer,
		]);
	}

	public static function kill_messages_default_player_void(Translatable|string $player, Translatable|string $killer) : Translatable{
		return new Translatable(CollapseTranslationKeys::KILL_MESSAGES_DEFAULT_PLAYER_VOID, [
			'player' => $player,
			'killer' => $killer,
		]);
	}

	public static function kill_messages_default_unknown(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::KILL_MESSAGES_DEFAULT_UNKNOWN, [
			'player' => $player,
		]);
	}

	public static function kill_messages_default_void(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::KILL_MESSAGES_DEFAULT_VOID, [
			'player' => $player,
		]);
	}

	public static function kill_reward(Translatable|string $amount) : Translatable{
		return new Translatable(CollapseTranslationKeys::KILL_REWARD, [
			'amount' => $amount,
		]);
	}

	public static function kit_editor_editing_started(Translatable|string $kit) : Translatable{
		return new Translatable(CollapseTranslationKeys::KIT_EDITOR_EDITING_STARTED, [
			'kit' => $kit,
		]);
	}

	public static function kit_editor_editing_stopped(Translatable|string $kit) : Translatable{
		return new Translatable(CollapseTranslationKeys::KIT_EDITOR_EDITING_STOPPED, [
			'kit' => $kit,
		]);
	}

	public static function kit_editor_form_kit_editor_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::KIT_EDITOR_FORM_KIT_EDITOR_TITLE, []);
	}

	public static function leaderboard_free_for_all_form_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::LEADERBOARD_FREE_FOR_ALL_FORM_TITLE, []);
	}

	public static function leaderboard_free_for_all_global_form_button() : Translatable{
		return new Translatable(CollapseTranslationKeys::LEADERBOARD_FREE_FOR_ALL_GLOBAL_FORM_BUTTON, []);
	}

	public static function leaderboard_free_for_all_local_form_button(Translatable|string $mode) : Translatable{
		return new Translatable(CollapseTranslationKeys::LEADERBOARD_FREE_FOR_ALL_LOCAL_FORM_BUTTON, [
			'mode' => $mode,
		]);
	}

	public static function leaderboard_ranked_duels_form_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::LEADERBOARD_RANKED_DUELS_FORM_TITLE, []);
	}

	public static function leaderboard_ranked_duels_global_form_button() : Translatable{
		return new Translatable(CollapseTranslationKeys::LEADERBOARD_RANKED_DUELS_GLOBAL_FORM_BUTTON, []);
	}

	public static function leaderboard_ranked_duels_local_form_button(Translatable|string $mode) : Translatable{
		return new Translatable(CollapseTranslationKeys::LEADERBOARD_RANKED_DUELS_LOCAL_FORM_BUTTON, [
			'mode' => $mode,
		]);
	}

	public static function leaderboard_unranked_duels_form_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::LEADERBOARD_UNRANKED_DUELS_FORM_TITLE, []);
	}

	public static function leaderboard_unranked_duels_global_best_win_streak_form_button() : Translatable{
		return new Translatable(CollapseTranslationKeys::LEADERBOARD_UNRANKED_DUELS_GLOBAL_BEST_WIN_STREAK_FORM_BUTTON, []);
	}

	public static function leaderboard_unranked_duels_global_form_button() : Translatable{
		return new Translatable(CollapseTranslationKeys::LEADERBOARD_UNRANKED_DUELS_GLOBAL_FORM_BUTTON, []);
	}

	public static function leaderboard_unranked_duels_local_best_win_streak_form_button(Translatable|string $mode) : Translatable{
		return new Translatable(CollapseTranslationKeys::LEADERBOARD_UNRANKED_DUELS_LOCAL_BEST_WIN_STREAK_FORM_BUTTON, [
			'mode' => $mode,
		]);
	}

	public static function leaderboard_unranked_duels_local_form_button(Translatable|string $mode) : Translatable{
		return new Translatable(CollapseTranslationKeys::LEADERBOARD_UNRANKED_DUELS_LOCAL_FORM_BUTTON, [
			'mode' => $mode,
		]);
	}

	public static function leaderboards_form_button_free_for_all_kills() : Translatable{
		return new Translatable(CollapseTranslationKeys::LEADERBOARDS_FORM_BUTTON_FREE_FOR_ALL_KILLS, []);
	}

	public static function leaderboards_form_button_ranked_elo() : Translatable{
		return new Translatable(CollapseTranslationKeys::LEADERBOARDS_FORM_BUTTON_RANKED_ELO, []);
	}

	public static function leaderboards_form_button_unranked_duels_best_win_streak() : Translatable{
		return new Translatable(CollapseTranslationKeys::LEADERBOARDS_FORM_BUTTON_UNRANKED_DUELS_BEST_WIN_STREAK, []);
	}

	public static function leaderboards_form_button_unranked_duels_wins() : Translatable{
		return new Translatable(CollapseTranslationKeys::LEADERBOARDS_FORM_BUTTON_UNRANKED_DUELS_WINS, []);
	}

	public static function leaderboards_form_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::LEADERBOARDS_FORM_TITLE, []);
	}

	public static function lobby_item_cosmetics() : Translatable{
		return new Translatable(CollapseTranslationKeys::LOBBY_ITEM_COSMETICS, []);
	}

	public static function lobby_item_duels() : Translatable{
		return new Translatable(CollapseTranslationKeys::LOBBY_ITEM_DUELS, []);
	}

	public static function lobby_item_free_for_all() : Translatable{
		return new Translatable(CollapseTranslationKeys::LOBBY_ITEM_FREE_FOR_ALL, []);
	}

	public static function lobby_item_leaderboards() : Translatable{
		return new Translatable(CollapseTranslationKeys::LOBBY_ITEM_LEADERBOARDS, []);
	}

	public static function lobby_item_profile() : Translatable{
		return new Translatable(CollapseTranslationKeys::LOBBY_ITEM_PROFILE, []);
	}

	public static function lobby_item_quests() : Translatable{
		return new Translatable(CollapseTranslationKeys::LOBBY_ITEM_QUESTS, []);
	}

	public static function lobby_item_shop() : Translatable{
		return new Translatable(CollapseTranslationKeys::LOBBY_ITEM_SHOP, []);
	}

	public static function lobby_npc_duels_already_in_queue() : Translatable{
		return new Translatable(CollapseTranslationKeys::LOBBY_NPC_DUELS_ALREADY_IN_QUEUE, []);
	}

	public static function lobby_npc_duels_fireball_fight_nametag_online(Translatable|string $playing) : Translatable{
		return new Translatable(CollapseTranslationKeys::LOBBY_NPC_DUELS_FIREBALL_FIGHT_NAMETAG_ONLINE, [
			'playing' => $playing,
		]);
	}

	public static function lobby_npc_duels_menu(Translatable|string $playing) : Translatable{
		return new Translatable(CollapseTranslationKeys::LOBBY_NPC_DUELS_MENU, [
			'playing' => $playing,
		]);
	}

	public static function lobby_npc_ffa_build(Translatable|string $playing) : Translatable{
		return new Translatable(CollapseTranslationKeys::LOBBY_NPC_FFA_BUILD, [
			'playing' => $playing,
		]);
	}

	public static function lobby_npc_ffa_menu(Translatable|string $playing) : Translatable{
		return new Translatable(CollapseTranslationKeys::LOBBY_NPC_FFA_MENU, [
			'playing' => $playing,
		]);
	}

	public static function lobby_npc_ffa_no_debuff_nametag_online(Translatable|string $playing) : Translatable{
		return new Translatable(CollapseTranslationKeys::LOBBY_NPC_FFA_NO_DEBUFF_NAMETAG_ONLINE, [
			'playing' => $playing,
		]);
	}

	public static function lobby_scoreboard_dust(Translatable|string $dust) : Translatable{
		return new Translatable(CollapseTranslationKeys::LOBBY_SCOREBOARD_DUST, [
			'dust' => $dust,
		]);
	}

	public static function lobby_scoreboard_games() : Translatable{
		return new Translatable(CollapseTranslationKeys::LOBBY_SCOREBOARD_GAMES, []);
	}

	public static function lobby_scoreboard_kd(Translatable|string $kills, Translatable|string $deaths) : Translatable{
		return new Translatable(CollapseTranslationKeys::LOBBY_SCOREBOARD_KD, [
			'kills' => $kills,
			'deaths' => $deaths,
		]);
	}

	public static function lobby_scoreboard_kdr(Translatable|string $kdr) : Translatable{
		return new Translatable(CollapseTranslationKeys::LOBBY_SCOREBOARD_KDR, [
			'kdr' => $kdr,
		]);
	}

	public static function lobby_scoreboard_online(Translatable|string $param0) : Translatable{
		return new Translatable(CollapseTranslationKeys::LOBBY_SCOREBOARD_ONLINE, [
			0 => $param0,
		]);
	}

	public static function lobby_scoreboard_playing(Translatable|string $param0) : Translatable{
		return new Translatable(CollapseTranslationKeys::LOBBY_SCOREBOARD_PLAYING, [
			0 => $param0,
		]);
	}

	public static function lobby_scoreboard_profile() : Translatable{
		return new Translatable(CollapseTranslationKeys::LOBBY_SCOREBOARD_PROFILE, []);
	}

	public static function lobby_scoreboard_wins(Translatable|string $wins) : Translatable{
		return new Translatable(CollapseTranslationKeys::LOBBY_SCOREBOARD_WINS, [
			'wins' => $wins,
		]);
	}

	public static function mention_subtitle() : Translatable{
		return new Translatable(CollapseTranslationKeys::MENTION_SUBTITLE, []);
	}

	public static function mention_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::MENTION_TITLE, []);
	}

	public static function mute_broadcast_admins(Translatable|string $player, Translatable|string $sender, Translatable|string $reason, Translatable|string $expiresAt) : Translatable{
		return new Translatable(CollapseTranslationKeys::MUTE_BROADCAST_ADMINS, [
			'player' => $player,
			'sender' => $sender,
			'reason' => $reason,
			'expiresAt' => $expiresAt,
		]);
	}

	public static function mute_broadcast_players(Translatable|string $player, Translatable|string $reason) : Translatable{
		return new Translatable(CollapseTranslationKeys::MUTE_BROADCAST_PLAYERS, [
			'player' => $player,
			'reason' => $reason,
		]);
	}

	public static function no_permission() : Translatable{
		return new Translatable(CollapseTranslationKeys::NO_PERMISSION, []);
	}

	public static function observe_armor_take_off() : Translatable{
		return new Translatable(CollapseTranslationKeys::OBSERVE_ARMOR_TAKE_OFF, []);
	}

	public static function observe_item_armor_take_off() : Translatable{
		return new Translatable(CollapseTranslationKeys::OBSERVE_ITEM_ARMOR_TAKE_OFF, []);
	}

	public static function observe_item_teleport() : Translatable{
		return new Translatable(CollapseTranslationKeys::OBSERVE_ITEM_TELEPORT, []);
	}

	public static function observe_scoreboard_input() : Translatable{
		return new Translatable(CollapseTranslationKeys::OBSERVE_SCOREBOARD_INPUT, []);
	}

	public static function observe_scoreboard_input_name(Translatable|string $input) : Translatable{
		return new Translatable(CollapseTranslationKeys::OBSERVE_SCOREBOARD_INPUT_NAME, [
			'input' => $input,
		]);
	}

	public static function observe_scoreboard_os(Translatable|string $os) : Translatable{
		return new Translatable(CollapseTranslationKeys::OBSERVE_SCOREBOARD_OS, [
			'os' => $os,
		]);
	}

	public static function observe_scoreboard_ping(Translatable|string $ping) : Translatable{
		return new Translatable(CollapseTranslationKeys::OBSERVE_SCOREBOARD_PING, [
			'ping' => $ping,
		]);
	}

	public static function observe_scoreboard_player() : Translatable{
		return new Translatable(CollapseTranslationKeys::OBSERVE_SCOREBOARD_PLAYER, []);
	}

	public static function observe_scoreboard_player_name(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::OBSERVE_SCOREBOARD_PLAYER_NAME, [
			'player' => $player,
		]);
	}

	public static function observe_scoreboard_status(Translatable|string $status) : Translatable{
		return new Translatable(CollapseTranslationKeys::OBSERVE_SCOREBOARD_STATUS, [
			'status' => $status,
		]);
	}

	public static function only_in_lobby() : Translatable{
		return new Translatable(CollapseTranslationKeys::ONLY_IN_LOBBY, []);
	}

	public static function party_is_full() : Translatable{
		return new Translatable(CollapseTranslationKeys::PARTY_IS_FULL, []);
	}

	public static function party_member_invite(Translatable|string $leader) : Translatable{
		return new Translatable(CollapseTranslationKeys::PARTY_MEMBER_INVITE, [
			'leader' => $leader,
		]);
	}

	public static function party_member_invite_broadcast(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::PARTY_MEMBER_INVITE_BROADCAST, [
			'player' => $player,
		]);
	}

	public static function party_member_join(Translatable|string $player, Translatable|string $size, Translatable|string $maxSize) : Translatable{
		return new Translatable(CollapseTranslationKeys::PARTY_MEMBER_JOIN, [
			'player' => $player,
			'size' => $size,
			'maxSize' => $maxSize,
		]);
	}

	public static function player_not_found() : Translatable{
		return new Translatable(CollapseTranslationKeys::PLAYER_NOT_FOUND, []);
	}

	public static function player_not_in_lobby() : Translatable{
		return new Translatable(CollapseTranslationKeys::PLAYER_NOT_IN_LOBBY, []);
	}

	public static function player_not_registered() : Translatable{
		return new Translatable(CollapseTranslationKeys::PLAYER_NOT_REGISTERED, []);
	}

	public static function player_not_staff() : Translatable{
		return new Translatable(CollapseTranslationKeys::PLAYER_NOT_STAFF, []);
	}

	public static function potion_moveSpeed() : Translatable{
		return new Translatable(CollapseTranslationKeys::POTION_MOVESPEED, []);
	}

	public static function potion_color_black() : Translatable{
		return new Translatable(CollapseTranslationKeys::POTION_COLOR_BLACK, []);
	}

	public static function potion_color_blue() : Translatable{
		return new Translatable(CollapseTranslationKeys::POTION_COLOR_BLUE, []);
	}

	public static function potion_color_green() : Translatable{
		return new Translatable(CollapseTranslationKeys::POTION_COLOR_GREEN, []);
	}

	public static function potion_color_pink() : Translatable{
		return new Translatable(CollapseTranslationKeys::POTION_COLOR_PINK, []);
	}

	public static function potion_color_selection_form_dropdown() : Translatable{
		return new Translatable(CollapseTranslationKeys::POTION_COLOR_SELECTION_FORM_DROPDOWN, []);
	}

	public static function potion_color_selection_form_none() : Translatable{
		return new Translatable(CollapseTranslationKeys::POTION_COLOR_SELECTION_FORM_NONE, []);
	}

	public static function potion_color_selection_form_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::POTION_COLOR_SELECTION_FORM_TITLE, []);
	}

	public static function potion_color_unavailable(Translatable|string $rank) : Translatable{
		return new Translatable(CollapseTranslationKeys::POTION_COLOR_UNAVAILABLE, [
			'rank' => $rank,
		]);
	}

	public static function potion_color_white() : Translatable{
		return new Translatable(CollapseTranslationKeys::POTION_COLOR_WHITE, []);
	}

	public static function potion_color_yellow() : Translatable{
		return new Translatable(CollapseTranslationKeys::POTION_COLOR_YELLOW, []);
	}

	public static function private_message_from(Translatable|string $player, Translatable|string $msg) : Translatable{
		return new Translatable(CollapseTranslationKeys::PRIVATE_MESSAGE_FROM, [
			'player' => $player,
			'msg' => $msg,
		]);
	}

	public static function private_message_to(Translatable|string $player, Translatable|string $msg) : Translatable{
		return new Translatable(CollapseTranslationKeys::PRIVATE_MESSAGE_TO, [
			'player' => $player,
			'msg' => $msg,
		]);
	}

	public static function private_messages_disabled(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::PRIVATE_MESSAGES_DISABLED, [
			'player' => $player,
		]);
	}

	public static function profile_form_button_free_for_all_statistics() : Translatable{
		return new Translatable(CollapseTranslationKeys::PROFILE_FORM_BUTTON_FREE_FOR_ALL_STATISTICS, []);
	}

	public static function profile_form_button_friends() : Translatable{
		return new Translatable(CollapseTranslationKeys::PROFILE_FORM_BUTTON_FRIENDS, []);
	}

	public static function profile_form_button_kit_editor() : Translatable{
		return new Translatable(CollapseTranslationKeys::PROFILE_FORM_BUTTON_KIT_EDITOR, []);
	}

	public static function profile_form_button_ranked_duels_statistics() : Translatable{
		return new Translatable(CollapseTranslationKeys::PROFILE_FORM_BUTTON_RANKED_DUELS_STATISTICS, []);
	}

	public static function profile_form_button_settings() : Translatable{
		return new Translatable(CollapseTranslationKeys::PROFILE_FORM_BUTTON_SETTINGS, []);
	}

	public static function profile_form_button_unranked_duels_statistics() : Translatable{
		return new Translatable(CollapseTranslationKeys::PROFILE_FORM_BUTTON_UNRANKED_DUELS_STATISTICS, []);
	}

	public static function profile_form_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::PROFILE_FORM_TITLE, []);
	}

	public static function punishment_expires_never() : Translatable{
		return new Translatable(CollapseTranslationKeys::PUNISHMENT_EXPIRES_NEVER, []);
	}

	public static function quest_concrete_ffa_nodebuff_kills_without_pots_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::QUEST_CONCRETE_FFA_NODEBUFF_KILLS_WITHOUT_POTS_DESCRIPTION, []);
	}

	public static function quest_concrete_ffa_nodebuff_kills_without_pots_form_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::QUEST_CONCRETE_FFA_NODEBUFF_KILLS_WITHOUT_POTS_FORM_DESCRIPTION, []);
	}

	public static function quest_concrete_ffa_nodebuff_kills_without_pots_form_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::QUEST_CONCRETE_FFA_NODEBUFF_KILLS_WITHOUT_POTS_FORM_TITLE, []);
	}

	public static function quest_concrete_ffa_nodebuff_kills_without_pots_name() : Translatable{
		return new Translatable(CollapseTranslationKeys::QUEST_CONCRETE_FFA_NODEBUFF_KILLS_WITHOUT_POTS_NAME, []);
	}

	public static function quest_concrete_ffa_sumo_30_kills_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::QUEST_CONCRETE_FFA_SUMO_30_KILLS_DESCRIPTION, []);
	}

	public static function quest_concrete_ffa_sumo_30_kills_form_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::QUEST_CONCRETE_FFA_SUMO_30_KILLS_FORM_DESCRIPTION, []);
	}

	public static function quest_concrete_ffa_sumo_30_kills_form_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::QUEST_CONCRETE_FFA_SUMO_30_KILLS_FORM_TITLE, []);
	}

	public static function quest_concrete_ffa_sumo_30_kills_name() : Translatable{
		return new Translatable(CollapseTranslationKeys::QUEST_CONCRETE_FFA_SUMO_30_KILLS_NAME, []);
	}

	public static function quest_concrete_first_join_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::QUEST_CONCRETE_FIRST_JOIN_DESCRIPTION, []);
	}

	public static function quest_concrete_first_join_name() : Translatable{
		return new Translatable(CollapseTranslationKeys::QUEST_CONCRETE_FIRST_JOIN_NAME, []);
	}

	public static function quest_form_available_quests_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::QUEST_FORM_AVAILABLE_QUESTS_TITLE, []);
	}

	public static function quest_progress_display_broken(Translatable|string $broken) : Translatable{
		return new Translatable(CollapseTranslationKeys::QUEST_PROGRESS_DISPLAY_BROKEN, [
			'broken' => $broken,
		]);
	}

	public static function quest_progress_display_completed() : Translatable{
		return new Translatable(CollapseTranslationKeys::QUEST_PROGRESS_DISPLAY_COMPLETED, []);
	}

	public static function quest_progress_display_killed(Translatable|string $killed) : Translatable{
		return new Translatable(CollapseTranslationKeys::QUEST_PROGRESS_DISPLAY_KILLED, [
			'killed' => $killed,
		]);
	}

	public static function quest_progress_display_placed(Translatable|string $placed) : Translatable{
		return new Translatable(CollapseTranslationKeys::QUEST_PROGRESS_DISPLAY_PLACED, [
			'placed' => $placed,
		]);
	}

	public static function queue_cancelled() : Translatable{
		return new Translatable(CollapseTranslationKeys::QUEUE_CANCELLED, []);
	}

	public static function queue_scoreboard_queue(Translatable|string $time) : Translatable{
		return new Translatable(CollapseTranslationKeys::QUEUE_SCOREBOARD_QUEUE, [
			'time' => $time,
		]);
	}

	public static function ranked_duels_form_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::RANKED_DUELS_FORM_TITLE, []);
	}

	public static function report_staff_new(Translatable|string $who, Translatable|string $target, Translatable|string $reason) : Translatable{
		return new Translatable(CollapseTranslationKeys::REPORT_STAFF_NEW, [
			'who' => $who,
			'target' => $target,
			'reason' => $reason,
		]);
	}

	public static function respawn_base_progress_subtitle(Translatable|string $param0) : Translatable{
		return new Translatable(CollapseTranslationKeys::RESPAWN_BASE_PROGRESS_SUBTITLE, [
			0 => $param0,
		]);
	}

	public static function respawn_base_progress_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::RESPAWN_BASE_PROGRESS_TITLE, []);
	}

	public static function respawn_base_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::RESPAWN_BASE_TITLE, []);
	}

	public static function rule_1() : Translatable{
		return new Translatable(CollapseTranslationKeys::RULE_1, []);
	}

	public static function rule_1_cropped() : Translatable{
		return new Translatable(CollapseTranslationKeys::RULE_1_CROPPED, []);
	}

	public static function server_restart_chat(Translatable|string $plural) : Translatable{
		return new Translatable(CollapseTranslationKeys::SERVER_RESTART_CHAT, [
			'plural' => $plural,
		]);
	}

	public static function server_restart_kick_message() : Translatable{
		return new Translatable(CollapseTranslationKeys::SERVER_RESTART_KICK_MESSAGE, []);
	}

	public static function setting_auto_respawn() : Translatable{
		return new Translatable(CollapseTranslationKeys::SETTING_AUTO_RESPAWN, []);
	}

	public static function setting_auto_sprint() : Translatable{
		return new Translatable(CollapseTranslationKeys::SETTING_AUTO_SPRINT, []);
	}

	public static function setting_filter_obscene_lexis() : Translatable{
		return new Translatable(CollapseTranslationKeys::SETTING_FILTER_OBSCENE_LEXIS, []);
	}

	public static function setting_hide_players_in_free_for_all() : Translatable{
		return new Translatable(CollapseTranslationKeys::SETTING_HIDE_PLAYERS_IN_FREE_FOR_ALL, []);
	}

	public static function setting_hide_scoreboard() : Translatable{
		return new Translatable(CollapseTranslationKeys::SETTING_HIDE_SCOREBOARD, []);
	}

	public static function setting_private_messages() : Translatable{
		return new Translatable(CollapseTranslationKeys::SETTING_PRIVATE_MESSAGES, []);
	}

	public static function settings_form_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::SETTINGS_FORM_TITLE, []);
	}

	public static function settings_saved() : Translatable{
		return new Translatable(CollapseTranslationKeys::SETTINGS_SAVED, []);
	}

	public static function shop_button_exchange() : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_BUTTON_EXCHANGE, []);
	}

	public static function shop_category_capes_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_CATEGORY_CAPES_DESCRIPTION, []);
	}

	public static function shop_category_capes_name() : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_CATEGORY_CAPES_NAME, []);
	}

	public static function shop_category_chat_tags_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_CATEGORY_CHAT_TAGS_DESCRIPTION, []);
	}

	public static function shop_category_chat_tags_name() : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_CATEGORY_CHAT_TAGS_NAME, []);
	}

	public static function shop_category_death_effects_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_CATEGORY_DEATH_EFFECTS_DESCRIPTION, []);
	}

	public static function shop_category_death_effects_name() : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_CATEGORY_DEATH_EFFECTS_NAME, []);
	}

	public static function shop_category_potion_colors_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_CATEGORY_POTION_COLORS_DESCRIPTION, []);
	}

	public static function shop_category_potion_colors_name() : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_CATEGORY_POTION_COLORS_NAME, []);
	}

	public static function shop_category_ranks_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_CATEGORY_RANKS_DESCRIPTION, []);
	}

	public static function shop_category_ranks_name() : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_CATEGORY_RANKS_NAME, []);
	}

	public static function shop_form_already_has_item_text() : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_FORM_ALREADY_HAS_ITEM_TEXT, []);
	}

	public static function shop_form_button_buy() : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_FORM_BUTTON_BUY, []);
	}

	public static function shop_form_button_category(Translatable|string $category, Translatable|string $purchased, Translatable|string $total) : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_FORM_BUTTON_CATEGORY, [
			'category' => $category,
			'purchased' => $purchased,
			'total' => $total,
		]);
	}

	public static function shop_form_button_category_without_stats(Translatable|string $category) : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_FORM_BUTTON_CATEGORY_WITHOUT_STATS, [
			'category' => $category,
		]);
	}

	public static function shop_form_item_price(Translatable|string $price) : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_FORM_ITEM_PRICE, [
			'price' => $price,
		]);
	}

	public static function shop_form_open_category_text() : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_FORM_OPEN_CATEGORY_TEXT, []);
	}

	public static function shop_form_text() : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_FORM_TEXT, []);
	}

	public static function shop_form_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_FORM_TITLE, []);
	}

	public static function shop_insufficient_dust() : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_INSUFFICIENT_DUST, []);
	}

	public static function shop_item_already_purchased() : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_ITEM_ALREADY_PURCHASED, []);
	}

	public static function shop_item_cape_description(Translatable|string $desc) : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_ITEM_CAPE_DESCRIPTION, [
			'desc' => $desc,
		]);
	}

	public static function shop_item_cape_name(Translatable|string $name) : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_ITEM_CAPE_NAME, [
			'name' => $name,
		]);
	}

	public static function shop_item_chat_tag_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_ITEM_CHAT_TAG_DESCRIPTION, []);
	}

	public static function shop_item_chat_tag_name(Translatable|string $name) : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_ITEM_CHAT_TAG_NAME, [
			'name' => $name,
		]);
	}

	public static function shop_item_death_effect_description(Translatable|string $desc) : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_ITEM_DEATH_EFFECT_DESCRIPTION, [
			'desc' => $desc,
		]);
	}

	public static function shop_item_death_effect_name(Translatable|string $name) : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_ITEM_DEATH_EFFECT_NAME, [
			'name' => $name,
		]);
	}

	public static function shop_item_potion_color_description() : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_ITEM_POTION_COLOR_DESCRIPTION, []);
	}

	public static function shop_item_potion_color_name(Translatable|string $name) : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_ITEM_POTION_COLOR_NAME, [
			'name' => $name,
		]);
	}

	public static function shop_item_ranks_arcane(Translatable|string $font) : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_ITEM_RANKS_ARCANE, [
			'font' => $font,
		]);
	}

	public static function shop_item_ranks_arcane_description(Translatable|string $display, Translatable|string $price) : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_ITEM_RANKS_ARCANE_DESCRIPTION, [
			'display' => $display,
			'price' => $price,
		]);
	}

	public static function shop_item_ranks_blazing(Translatable|string $font) : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_ITEM_RANKS_BLAZING, [
			'font' => $font,
		]);
	}

	public static function shop_item_ranks_blazing_description(Translatable|string $display, Translatable|string $price) : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_ITEM_RANKS_BLAZING_DESCRIPTION, [
			'display' => $display,
			'price' => $price,
		]);
	}

	public static function shop_item_ranks_celestial(Translatable|string $font) : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_ITEM_RANKS_CELESTIAL, [
			'font' => $font,
		]);
	}

	public static function shop_item_ranks_celestial_description(Translatable|string $display, Translatable|string $price) : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_ITEM_RANKS_CELESTIAL_DESCRIPTION, [
			'display' => $display,
			'price' => $price,
		]);
	}

	public static function shop_item_ranks_description(Translatable|string $font) : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_ITEM_RANKS_DESCRIPTION, [
			'font' => $font,
		]);
	}

	public static function shop_item_ranks_ethereum(Translatable|string $font) : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_ITEM_RANKS_ETHEREUM, [
			'font' => $font,
		]);
	}

	public static function shop_item_ranks_ethereum_description(Translatable|string $display, Translatable|string $price) : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_ITEM_RANKS_ETHEREUM_DESCRIPTION, [
			'display' => $display,
			'price' => $price,
		]);
	}

	public static function shop_item_ranks_have_rank_already() : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_ITEM_RANKS_HAVE_RANK_ALREADY, []);
	}

	public static function shop_item_ranks_luminous(Translatable|string $font) : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_ITEM_RANKS_LUMINOUS, [
			'font' => $font,
		]);
	}

	public static function shop_item_ranks_luminous_description(Translatable|string $display, Translatable|string $price) : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_ITEM_RANKS_LUMINOUS_DESCRIPTION, [
			'display' => $display,
			'price' => $price,
		]);
	}

	public static function shop_purchase_success(Translatable|string $item) : Translatable{
		return new Translatable(CollapseTranslationKeys::SHOP_PURCHASE_SUCCESS, [
			'item' => $item,
		]);
	}

	public static function team_blue_name() : Translatable{
		return new Translatable(CollapseTranslationKeys::TEAM_BLUE_NAME, []);
	}

	public static function team_red_name() : Translatable{
		return new Translatable(CollapseTranslationKeys::TEAM_RED_NAME, []);
	}

	public static function time_day_cases() : Translatable{
		return new Translatable(CollapseTranslationKeys::TIME_DAY_CASES, []);
	}

	public static function time_day_short() : Translatable{
		return new Translatable(CollapseTranslationKeys::TIME_DAY_SHORT, []);
	}

	public static function time_hour_cases() : Translatable{
		return new Translatable(CollapseTranslationKeys::TIME_HOUR_CASES, []);
	}

	public static function time_hour_short() : Translatable{
		return new Translatable(CollapseTranslationKeys::TIME_HOUR_SHORT, []);
	}

	public static function time_minute_cases() : Translatable{
		return new Translatable(CollapseTranslationKeys::TIME_MINUTE_CASES, []);
	}

	public static function time_minute_short() : Translatable{
		return new Translatable(CollapseTranslationKeys::TIME_MINUTE_SHORT, []);
	}

	public static function time_second_cases() : Translatable{
		return new Translatable(CollapseTranslationKeys::TIME_SECOND_CASES, []);
	}

	public static function time_second_short() : Translatable{
		return new Translatable(CollapseTranslationKeys::TIME_SECOND_SHORT, []);
	}

	public static function unban_broadcast_admins(Translatable|string $player, Translatable|string $sender) : Translatable{
		return new Translatable(CollapseTranslationKeys::UNBAN_BROADCAST_ADMINS, [
			'player' => $player,
			'sender' => $sender,
		]);
	}

	public static function unban_broadcast_players(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::UNBAN_BROADCAST_PLAYERS, [
			'player' => $player,
		]);
	}

	public static function unmute_broadcast_admins(Translatable|string $player, Translatable|string $sender) : Translatable{
		return new Translatable(CollapseTranslationKeys::UNMUTE_BROADCAST_ADMINS, [
			'player' => $player,
			'sender' => $sender,
		]);
	}

	public static function unmute_broadcast_players(Translatable|string $player) : Translatable{
		return new Translatable(CollapseTranslationKeys::UNMUTE_BROADCAST_PLAYERS, [
			'player' => $player,
		]);
	}

	public static function unranked_duels_form_title() : Translatable{
		return new Translatable(CollapseTranslationKeys::UNRANKED_DUELS_FORM_TITLE, []);
	}

	public static function welcome_message(Translatable|string $name) : Translatable{
		return new Translatable(CollapseTranslationKeys::WELCOME_MESSAGE, [
			'name' => $name,
		]);
	}

	public static function you_cant_punish_player() : Translatable{
		return new Translatable(CollapseTranslationKeys::YOU_CANT_PUNISH_PLAYER, []);
	}

}

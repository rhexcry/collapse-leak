<?php

declare(strict_types=1);

namespace collapse\wallet\command;

use collapse\command\CollapseCommand;
use collapse\command\CommandArguments;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\player\profile\trait\PlayerProfileResolver;
use collapse\player\rank\attribute\RequiresRank;
use collapse\player\rank\Rank;
use collapse\Practice;
use collapse\wallet\currency\Currencies;
use collapse\wallet\Wallet;
use collapse\wallet\WalletManager;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use function array_keys;
use function count;
use function is_numeric;

#[RequiresRank(Rank::ADMIN)]
final class CurrencyCommand extends CollapseCommand{

	use PlayerProfileResolver;

	public function __construct(private readonly WalletManager $walletManager){
		parent::__construct('currency', 'Currency management');
		$this->setPermission('collapse.command.currency');
		$this->commandArguments = new CommandArguments();
		$this->commandArguments->addParameter(0, 'player', AvailableCommandsPacket::ARG_TYPE_TARGET);
		$this->commandArguments->addEnum(0, 'currency', array_keys(Currencies::getAll()));
		$this->commandArguments->addEnum(0, 'currency_action', ['set', 'add', 'reduce']);
		$this->commandArguments->addParameter(0, 'amount', AvailableCommandsPacket::ARG_TYPE_INT);
	}

	protected function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(count($args) < 4){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_currency_usage());
			return;
		}

		$player = Practice::getPlayerByPrefix($args[0]) ?? $args[0];
		$profile = self::resolveProfile($player);

		if($profile === null){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::player_not_registered());
			return;
		}

		$currency = Currencies::get($args[1]);
		if($currency === null){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_currency_unknown_currency());
			return;
		}

		if(!is_numeric($args[3])){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_currency_invalid_amount());
			return;
		}

		$amount = (int) $args[3];
		$previous = $profile->getCurrencyAmount($currency);

		switch($args[2]){
			case 'set':
				Wallet::set($currency, $profile, $amount);
				break;
			case 'add':
				Wallet::add($currency, $profile, $amount);
				break;
			case 'reduce':
				Wallet::reduce($currency, $profile, $amount);
				break;
			default:
				$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_currency_unknown_action());
				return;
		}

		$this->walletManager->getPlugin()->getSocialManager()->getStaffLogger()->onCurrencyChange(
			$sender,
			$profile,
			$currency,
			$previous,
			$profile->getCurrencyAmount($currency)
		);
		$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_currency_successfully(
			$profile->getRank()->toColor() . $profile->getPlayerName(),
			$currency->getName(),
			(string) $previous,
			(string) $profile->getCurrencyAmount($currency)
		));
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_currency_description();
	}
}

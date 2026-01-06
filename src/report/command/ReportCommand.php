<?php

declare(strict_types=1);

namespace collapse\report\command;

use collapse\command\attribute\OnlyForPlayerCommand;
use collapse\command\CollapseCommand;
use collapse\command\CommandArguments;
use collapse\i18n\CollapseTranslationFactory;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use collapse\report\Report;
use collapse\report\ReportData;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use function array_shift;
use function count;
use function implode;
use function uniqid;

#[OnlyForPlayerCommand]
final class ReportCommand extends CollapseCommand{

	public function __construct(){
		parent::__construct('report');
		$this->setPermission('collapse.command.report');
		$this->commandArguments = new CommandArguments();
		$this->commandArguments->addParameter(0, 'player', AvailableCommandsPacket::ARG_TYPE_TARGET);
		$this->commandArguments->addParameter(0, 'report message');
	}

	/**
	 * @param CollapsePlayer $sender
	 */
	protected function onExecute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(count($args) < 2){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_report_usage());
			return;
		}

		if(($player = Practice::getPlayerByPrefix(array_shift($args))) === null){
			$this->sendTranslatedMessage($sender, CollapseTranslationFactory::player_not_found());
			return;
		}

		$reportData = new ReportData(
			$sender->getProfile(),
			$player->getProfile(),
			implode(" ", $args),
			new \DateTimeImmutable()
		);

		Practice::getInstance()->getReportManager()->addReport(new Report(
			uniqid('report_'),
			$reportData
		));

		$this->sendTranslatedMessage($sender, CollapseTranslationFactory::command_report_successfully($player->getNameWithRankColor()));
	}

	public function getDescriptionForPlayer(CollapsePlayer $player) : Translatable{
		return CollapseTranslationFactory::command_report_description();
	}
}

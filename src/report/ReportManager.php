<?php

declare(strict_types=1);

namespace collapse\report;

use collapse\i18n\CollapseTranslationFactory;
use collapse\Practice;
use collapse\report\command\ReportCommand;
use collapse\report\command\ReportsCommand;
use collapse\social\logger\ReportLogger;
use pocketmine\Server;
use function array_filter;

final class ReportManager{

	/** @var Report[] */
	private array $reportMap = [];

	private ReportLogger $telegramReportLogger;

	public function __construct(){
		Server::getInstance()->getCommandMap()->registerAll('collapse', [
			new ReportCommand(),
			new ReportsCommand()
		]);

		$this->telegramReportLogger = Practice::getInstance()->getSocialManager()->getReportLogger();
	}

	public function addReport(Report $report) : void{
		$this->reportMap[$report->getId()] = $report;
		$this->notifyStaff($report);
	}

	public function getReport(string $id) : ?Report{
		return $this->reports[$id] ?? null;
	}

	/** @return Report[] */
	public function getActiveReports() : array{
		return array_filter($this->reportMap, fn($report) => $report->getStatus() === ReportStatus::OPEN);
	}

	public function updateReportStatus(string $id, ReportStatus $status) : bool{
		if($report = $this->getReport($id)){
			$report->setStatus($status);
			return true;
		}
		return false;
	}

	private function notifyStaff(Report $report) : void{
		foreach(Practice::onlinePlayers() as $player){
			if(!$player->isOnline()){
				continue;
			}

			if($player->getProfile()?->getRank()->isStaffRank() && $player->isConnected()){
				$player->sendTranslatedMessage(CollapseTranslationFactory::report_staff_new(
					$report->getData()->getReporter()->getPlayerName(),
					$report->getData()->getTarget()->getPlayerName(),
					$report->getData()->getMessage()
				));
			}
		}

		$this->telegramReportLogger->onReport($report);
	}
}

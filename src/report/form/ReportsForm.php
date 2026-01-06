<?php

declare(strict_types=1);

namespace collapse\report\form;

use collapse\form\SimpleForm;
use collapse\player\CollapsePlayer;
use collapse\Practice;
use function array_values;
use const EOL;

final class ReportsForm extends SimpleForm{

	public function __construct(){
		parent::__construct(function(CollapsePlayer $player, ?int $data = null) : void{
			if($data === null){
				return;
			}

			$report = array_values(Practice::getInstance()->getReportManager()->getActiveReports())[$data];

			//$player->sendForm(new AboutReportForm($report));
		});
		foreach(Practice::getInstance()->getReportManager()->getActiveReports() as $report){
			$reportData = $report->getData();
			$this->addButton(
				'ID: ' . $report->getId() . EOL .
				'Reporter: ' . $reportData->getReporter()->getPlayerName() . EOL .
				'Player: ' . $reportData->getTarget()->getPlayerName() . EOL .
				'Message: ' . $reportData->getMessage() . EOL .
				'Created at: ' . EOL . EOL . EOL
			);
		}
	}
}

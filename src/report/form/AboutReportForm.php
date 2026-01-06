<?php

declare(strict_types=1);

namespace collapse\report\form;

use collapse\form\CustomForm;
use collapse\player\CollapsePlayer;
use collapse\report\Report;
use function var_dump;
use const EOL;

final class AboutReportForm extends CustomForm{

	public function __construct(Report $report){
		parent::__construct(function(CollapsePlayer $player, mixed $data) use ($report) : void{
			var_dump($data);
			if($data === null){
				return;
			}
		});

		$reportData = $report->getData();
		$this->addLabel(
			'ID: ' . $report->getId() . EOL .
			'Reporter: ' . $reportData->getReporter()->getPlayerName() . EOL .
			'Player: ' . $reportData->getTarget()->getPlayerName() . EOL .
			'Message: ' . $reportData->getMessage() . EOL .
			'Created at: ' . EOL . EOL . EOL);

		$this->addInput('1', '2', '3');
	}
}

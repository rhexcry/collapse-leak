<?php

declare(strict_types=1);

namespace collapse\report;

final class Report{

	public function __construct(
		private readonly string $id,
		private readonly ReportData $data,
		private ReportStatus $status = ReportStatus::OPEN
	){
	}

	public function getId() : string{
		return $this->id;
	}

	public function getData() : ReportData{
		return $this->data;
	}

	public function getStatus() : ReportStatus{
		return $this->status;
	}

	public function setStatus(ReportStatus $status) : void{
		$this->status = $status;
	}
}

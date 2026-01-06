<?php

declare(strict_types=1);

namespace collapse\report;

use collapse\player\profile\Profile;
use DateTimeImmutable;

final readonly class ReportData{

	public function __construct(
		private Profile $reporter,
		private Profile $target,
		private string $message,
		private DateTimeImmutable $createdAt
	){
	}

	public function getReporter() : Profile{
		return $this->reporter;
	}

	public function getTarget() : Profile{
		return $this->target;
	}

	public function getMessage() : string{
		return $this->message;
	}

	public function getCreatedAt() : DateTimeImmutable{
		return $this->createdAt;
	}
}

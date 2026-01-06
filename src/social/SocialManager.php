<?php

declare(strict_types=1);

namespace collapse\social;

use collapse\Practice;
use collapse\social\entry\Telegram;
use collapse\social\logger\InternalLogger;
use collapse\social\logger\ReportLogger;
use collapse\social\logger\StaffLogger;

final class SocialManager{

	private Telegram $telegram;

	private StaffLogger $staffLogger;
	private ReportLogger $reportLogger;
	private InternalLogger $internalLogger;

	public function __construct(private readonly Practice $plugin){
		$this->telegram = new Telegram($this->plugin->getServer()->getLogger());

		$this->staffLogger = new StaffLogger($this);
		$this->reportLogger = new ReportLogger($this);
		$this->internalLogger = new InternalLogger($this);
	}

	public function getPlugin() : Practice{
		return $this->plugin;
	}

	public function getTelegram() : Telegram{
		return $this->telegram;
	}

	public function getStaffLogger() : StaffLogger{
		return $this->staffLogger;
	}

	public function getReportLogger() : ReportLogger{
		return $this->reportLogger;
	}

	public function getInternalLogger() : InternalLogger{
		return $this->internalLogger;
	}
}
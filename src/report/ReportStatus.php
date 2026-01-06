<?php

declare(strict_types=1);

namespace collapse\report;

enum ReportStatus{

	case OPEN;
	case RESOLVED;
	case REJECTED;
}
